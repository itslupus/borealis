<?php
    class User {
        private $id;
        private $last_login;

        public function __construct($id, $last_login) {
            $this->id = $id;
            $this->last_login = $last_login;
        }
    }
?>