<?php
    interface IDatabase {
        function get_user(int $id);
        function get_token(int $id);

        function insert_new_user(int $id);
        function insert_new_token(int $id, Token $token);

        function update_user_last_login(int $id);
        function update_token_timeout(int $id, int $timeout);

        function set_token_token(int $id, string $token);
    }
?>