<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/persistence/MySQL.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Token.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/CURL.php');

    class MrManager {
        private $config_data = null;

        public function __construct() {
            $this->config_data = parse_ini_file(__DIR__ . '/../_config.ini', true);

            $verify_keys = ['general', 'sql'];
            foreach ($verify_keys as $key) {
                if (!array_key_exists($key, $this->config_data)) {
                    throw new MrManagerInvalidConfig();
                }
            }
        }

        public function get_config() {
            return $this->config_data;
        }

        public function generate_tmp_file() {
            //TODO: un-hardcode this
            $tmp_folder = $this->config_data['general']['tmp_directory'];
            $tmp_prefix = $this->config_data['general']['tmp_prefix'];
            $tmp_file_path = tempnam('../' . $tmp_folder, $tmp_prefix . '-');

            return $tmp_file_path;
        }

        public function validate_token(string $token) {
            $sql = new MySQL($this->config_data);
            $token = $sql->get_token_by_token($token);

            if ($token === false) {
                throw new MrManagerInvalidToken();
            }

            if ($token->get_expires() < time()) {
                throw new MrManagerExpiredToken();
            }

            return $token;
        }

        public function validate_banner_session(Token $token) {
            if ($token->get_tmp_file_name() !== NULL) {
                //TODO: un-hardcode cookie file
                $main_url = $this->config_data['general']['main_url'];
                $session_file = $_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $token->get_tmp_file_name();

                $curl = new CURL($main_url, $session_file);
                $response = $curl->get_page('/banprod/twbkwbis.P_GenMenu?name=bmenu.P_MainMnu');

                $page = new Page($response);
                $result = $page->query('//form[contains(@name, "loginform")]');
                
                if (isset($result) && $result->length === 1) {
                    throw new MrManagerInvalidBannerSession();
                }
            }
        }

        public function regenerate_token(Token $token) {
            $sql = new MySQL($this->config_data);

            $token->generate_token();
            $sql->set_token_token($token->get_user(), $token->get_token());

            return $token;
        }
    }

    class MrManagerInvalidConfig extends Exception {}
    class MrManagerInvalidToken extends Exception {}
    class MrManagerExpiredToken extends Exception {}
    class MrManagerInvalidBannerSession extends Exception {}
?>