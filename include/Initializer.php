<?php
    require_once(__DIR__ . '/../object/CURL.php');
    require_once(__DIR__ . '/../object/Page.php');

    class Initializer {
        private $config_data;

        /*********************************
         * Create the initializer by reading in config
         * 
         * @throws  InitializerConfigInvalid if config file contains invalid/missing keys
         */
        public function __construct() {
            // $this->clean_tmp_files();

            $this->config_data = parse_ini_file(__DIR__ . '/../_config.ini', true);

            $verify_keys = ['general', 'sql'];
            foreach ($verify_keys as $key) {
                if (!array_key_exists($key, $this->config_data)) {
                    throw new InitializerConfigInvalid();
                }
            }
        }

        public function clean_tmp_files() {
            $files = scandir('../tmp');
            $prefix = 'borealis';

            foreach ($files as $file) {
                if (substr($file, 0, strlen($prefix)) !== $prefix) {
                    continue;
                }

                $file_path = '../tmp/' . $file;
                $mod_time = filemtime($file_path);
                if ($mod_time + 1440 <= time()) {
                    unlink($file_path);
                }
            }
        }

        /*********************************
         * Returns the configuration file
         * 
         * @returns returns a dictionary of config entries
         */
        public function get_config() {
            return $this->config_data;
        }

        public function start_session() {
            session_start();
        }

        /*********************************
         * Verifies if we have a valid session
         * 
         * This function will not do anything if the session is valid
         * 
         * @throws  InitializerConfigInvalid
         */
        public function verify_session() {
            $this->start_session();

            if (!isset($_SESSION['session_file'])) {
                throw new InitializerInvalidSession();
            }

            $main_url = $this->config_data['general']['main_url'];
            $session_file = $_SESSION['session_file'];

            $curl = new CURL($main_url, $session_file);
            $response = $curl->get_page('/banprod/twbkwbis.P_GenMenu?name=bmenu.P_MainMnu');

            $page = new Page($response);
            $result = $page->query('//form[contains(@name, "loginform")]');
            
            if (isset($result) && $result->length === 1) {
                // make sure to destory session first
                $this->destroy_session();

                throw new InitializerInvalidSession();
            }
        }

        public function destroy_session() {
            if (isset($_SESSION['session_file'])) {
                unlink($_SESSION['session_file']);
            }

            $_SESSION = array();
            session_destroy();
        }
    }

    class InitializerConfigInvalid extends Exception {}
    class InitializerInvalidSession extends Exception {}
?>