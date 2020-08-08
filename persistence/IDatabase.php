<?php
    interface IDatabase {
        function get_user(int $id);
        function get_token_by_id(int $id);
        function get_token_by_token(string $token);

        function insert_new_user(int $id);
        function insert_new_token(int $id, Token $token);

        function update_user_last_login(int $id);

        function update_token_token(int $id, string $token);

        function delete_token($id);
    }
?>