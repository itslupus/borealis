<?php
    require_once(__DIR__ . '/../persistence/MySQL.php');
    require_once(__DIR__ . '/../persistence/SQLite.php');

    require_once(__DIR__ . '/../object/Token.php');
    require_once(__DIR__ . '/../object/CURL.php');

    class MrManager {
        private $config_data;
        private $token = NULL;
        private $curl = NULL;

        public function __construct($cookie = NULL) {
            if (file_exists(__DIR__ . '/../_config.php') === false) {
                copy(__DIR__ . '/../_config.php.sample', __DIR__ . '/../_config.php');
            }

            $this->config_data = require_once(__DIR__ . '/../_config.php');

            if ($cookie !== NULL) {
                try {
                    $this->validate_token($cookie);
                    $this->regenerate_token();
            
                    $this->set_token_cookie();
                } catch (MrManagerInvalidToken $e) {
                    // 401 unauth
                    http_response_code(401);
                    die('invalid token');
                } catch (MrManagerExpiredToken $e2) {
                    // 401 unauth
                    http_response_code(401);
                    die('expired token');
                }
            }
        }

        public function get_config() {
            return $this->config_data;
        }

        public function generate_tmp_file() {
            $tmp_folder = $this->config_data['tmp_directory'];

            if (!file_exists($tmp_folder))
                mkdir($tmp_folder, 0700, true);

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

        public function get_page($resource, $data = NULL) {
            if ($this->curl === NULL) {
                $main_url = $this->config_data['main_url'];
                $tmp_path = $this->config_data['tmp_directory'];
                $user_agent = $this->config_data['user_agent'];

                if ($this->token === NULL) {
                    $tmp_file = $this->generate_tmp_file();
                    $tmp_file = explode('/', $tmp_file);
                    $tmp_file = end($tmp_file);
                    $tmp_path .= $tmp_file;
                } else {
                    $tmp_path .= $this->token->get_tmp_file_name();
                }

                $this->curl = new CURL($main_url, $tmp_path, $user_agent);
            }
            
            if ($data !== NULL) {
                $this->curl->set_post($data);
            }

            $page = new Page($this->curl->get_page($resource));

            $result = $page->query('//form[contains(@name, "loginform")]');
            
            if (isset($result) && $result->length === 1) {
                // 401 unauth
                http_response_code(401);
                die('invalid banner session');
            }

            return $page;
        }

        public function validate_token(string $token) {
            $token_obj = $this->generate_sql_connection()->get_token_by_token($token);

            if ($token_obj === false) {
                throw new MrManagerInvalidToken();
            }

            if ($token_obj->get_expires() < time()) {
                throw new MrManagerExpiredToken();
            }

            $this->token = $token_obj;
        }

        public function regenerate_token() {
            if ($this->token !== NULL) {
                $this->token->generate_token();

                $sql = $this->generate_sql_connection();
                $sql->update_token_token($this->token->get_user(), $this->token->get_token());
            }
        }

        public function set_token_cookie($token_obj = NULL) {
            if ($this->token !== NULL) {
                setcookie('token', $this->token->get_token(), $this->token->get_expires(), '/', '', false);
            } else if ($token_obj !== NULL) {
                setcookie('token', $token_obj->get_token(), $token_obj->get_expires(), '/', '', false);
            }
        }
    }

    class MrManagerInvalidToken extends Exception {}
    class MrManagerExpiredToken extends Exception {}
?>