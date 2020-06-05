<?php
    class Token {
        private $token;
        private $tmp_file_path;
        private $expires;

        public function generate_token() {
            $rand = random_int(PHP_INT_MIN, PHP_INT_MAX);
            $token = hash('md5', $rand);

            $this->token = $token;
        }

        public function set_token($token) {
            $this->token = $token;
        }

        public function set_tmp_file_path($file) {
            $this->tmp_file_path = $file;
        }

        public function set_expires($time) {
            $this->expires = $time;
        }

        public function get_token() {
            return $this->token;
        }

        public function get_tmp_file_path() {
            return $this->tmp_file_path;
        }

        public function get_expires() {
            return $this->expires;
        }
    }
?>