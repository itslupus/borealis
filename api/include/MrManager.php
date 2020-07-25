<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/persistence/MySQL.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Token.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/CURL.php');

    class MrManager {
        private $config_data = require_once($_SERVER['DOCUMENT_ROOT'] . '/api/_config.php');

        public function get_config() {
            return $this->config_data;
        }

        public function generate_tmp_file() {
            //TODO: un-hardcode this
            // $tmp_folder = $this->config_data['general']['tmp_directory'];
            $tmp_prefix = $this->config_data['tmp_prefix'];
            $tmp_file_path = tempnam($_SERVER['DOCUMENT_ROOT'] . '/api/tmp', $tmp_prefix . '-');

            return $tmp_file_path;
        }

        public function generate_sql_connection() {
            $sql = new MySQL(
                $this->config_data['mysql']['host'],
                $this->config_data['mysql']['username'],
                $this->config_data['mysql']['password'],
                $this->config_data['mysql']['table']
            );

            return $sql;
        }

        public function validate_token(string $token) {
            $token = $this->generate_sql_connection()->get_token_by_token($token);

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
                $main_url = $this->config_data['main_url'];
                $session_file = $_SERVER['DOCUMENT_ROOT'] . '/api/tmp/' . $token->get_tmp_file_name();
                $user_agent = $this->config_data['user_agent'];

                $curl = new CURL($main_url, $session_file, $user_agent);
                $response = $curl->get_page('/banprod/twbkwbis.P_GenMenu?name=bmenu.P_MainMnu');

                $page = new Page($response);
                $result = $page->query('//form[contains(@name, "loginform")]');
                
                if (isset($result) && $result->length === 1) {
                    throw new MrManagerInvalidBannerSession();
                }
            }
        }

        public function regenerate_token(Token $token) {
            $token->generate_token();
            $this->generate_sql_connection()->set_token_token($token->get_user(), $token->get_token());

            return $token;
        }

        public function set_token_cookie($token) {
            setcookie('token', $token->get_token(), $token->get_expires(), '/', '', false);
        }
    }

    class MrManagerInvalidToken extends Exception {}
    class MrManagerExpiredToken extends Exception {}
    class MrManagerInvalidBannerSession extends Exception {}
?>