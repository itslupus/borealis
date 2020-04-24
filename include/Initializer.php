<?php
    class Initializer {
        public function __construct() {
            // $this->clean_tmp_files();
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
         * Reads the configuration file
         * 
         * @returns returns a dictionary of config entries
         * @throws  InitializerConfigInvalid
         */
        public function read_config() {
            $config_data = parse_ini_file(__DIR__ . '/../_config.ini.php', true);

            $verify_keys = ['general', 'sql'];
            foreach ($verify_keys as $key) {
                if (!array_key_exists($key, $config_data)) {
                    throw new InitializerConfigInvalid();
                }
            }

            return $config_data;
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
        }

        public function destroy_session() {
            $_SESSION = array();
            session_destroy();
        }
    }

    class InitializerConfigInvalid extends Exception {}
    class InitializerInvalidSession extends Exception {}
?>