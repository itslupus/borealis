<?php
    class Token {
        private $user;
        private $token;
        private $tmp_file_name;
        private $expires;

        public function generate_token() {
            $rand = random_int(PHP_INT_MIN, PHP_INT_MAX);
            $token = hash('md5', $rand);

            $this->token = $token;
        }

        public function set_user($id) {
            $this->user = $id;
        }

        public function set_token($token) {
            $this->token = $token;
        }

        public function set_tmp_file_name($file) {
            $this->tmp_file_name = $file;
        }

        public function set_expires($time) {
            $this->expires = $time;
        }

        public function get_user() {
            return $this->user;
        }

        public function get_token() {
            return $this->token;
        }

        public function get_tmp_file_name() {
            return $this->tmp_file_name;
        }

        public function get_expires() {
            return $this->expires;
        }
    }
?>