<?php
    class User {
        private $id;
        private $first_login;
        private $last_login;

        public function __construct($id, $last_login, $first_login) {
            $this->id = $id;
            $this->first_login = $first_login;
            $this->last_login = $last_login;
        }

        public function get_first_term() {
            return $this->first_login;
        }
    }
?>