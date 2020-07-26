<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/persistence/MySQL.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/persistence/SQLite.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Token.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/CURL.php');

    class MrManager {
        private $config_data;

        public function __construct() {
            $this->config_data = require_once($_SERVER['DOCUMENT_ROOT'] . '/api/_config.php');
        }

        public function get_config() {
            return $this->config_data;
        }

        public function generate_tmp_file() {
            $tmp_folder = $this->config_data['tmp_directory'];

            if (!file_exists($tmp_folder))
                mkdir($tmp_folder, 0775, true);

            $tmp_prefix = $this->config_data['tmp_prefix'];
            $tmp_file_path = tempnam($tmp_folder, $tmp_prefix . '-');

            return $tmp_file_path;
        }

        public function generate_sql_connection() {
            if ($this->config_data['database_method'] === 'mysql') {
                return new MySQL(
                    $this->config_data['mysql']['host'],
                    $this->config_data['mysql']['username'],
                    $this->config_data['mysql']['password'],
                    $this->config_data['mysql']['table']
                );
            } else {
                return new SQLite(
                    $this->config_data['sqlite']['file']
                );
            }
        }

        public function get_curl_object($tmp_file) {
            $main_url = $this->config_data['main_url'];
            $tmp_path = $this->config_data['tmp_directory'] . $tmp_file;
            $user_agent = $this->config_data['user_agent'];

            return new CURL($main_url, $tmp_path, $user_agent);
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
                $curl = $this->get_curl_object($token->get_tmp_file_name());
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
            $this->generate_sql_connection()->update_token_token($token->get_user(), $token->get_token());

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