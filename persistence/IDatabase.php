<?php
    interface IDatabase {
        function get_user(int $id);
        function get_token(int $id);

        function insert_new_user(int $id);
        function insert_new_token(int $id, Token $token);

        function update_user_last_login(int $id);

        function set_token_token(int $id, string $token);

        function delete_token($id);
    }
?>