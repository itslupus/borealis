<?php
    require_once('IDatabase.php');
    require_once(__DIR__ . '/../object/User.php');
    require_once(__DIR__ . '/../object/Token.php');

    class MySQL implements IDatabase {
        private $db;

        public function __construct($host, $username, $password, $tbl_name) {
            $this->db = new PDO("mysql:host=$host;dbname=$tbl_name", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->create_user_table();
            $this->create_token_table();
        }

        private function create_user_table() {
            $query = '
                CREATE TABLE IF NOT EXISTS Users (
                    id INT NOT NULL UNIQUE PRIMARY KEY,
                    first_term UNSIGNED SMALLINT NOT NULL,
                    last_login INT UNSIGNED NOT NULL
                )
            ';

            $prepared = $this->db->prepare($query);
            $prepared->execute();
        }

        private function create_token_table() {
            $query = '
                CREATE TABLE IF NOT EXISTS Tokens (
                    id INT NOT NULL UNIQUE,
                    token CHAR(32) NOT NULL UNIQUE,
                    cookie_file char(32) NOT NULL UNIQUE,
                    expires INT UNSIGNED NOT NULL,
                    FOREIGN KEY (id) REFERENCES Users(id) ON DELETE CASCADE
                )
            ';

            $prepared = $this->db->prepare($query);
            $prepared->execute();
        }

        function get_user(int $id) {
            $query = 'SELECT * FROM Users WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->execute();

            if ($prepared->rowCount() === 1) {
                $result = $prepared->fetch(PDO::FETCH_ASSOC);
                
                return new User($result['id'], $result['last_login'], $result['first_term']);
            }

            return false;
        }

        function get_token_by_id(int $id) {
            $query = 'SELECT * FROM Tokens WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->execute();

            if ($prepared->rowCount() === 1) {
                $result = $prepared->fetch(PDO::FETCH_ASSOC);
                
                $token = new Token();
                $token->set_token($result['token']);
                $token->set_tmp_file_name($result['cookie_file']);
                $token->set_expires($result['expires']);

                return $token;
            }

            return false;
        }

        function get_token_by_token(string $token) {
            $query = 'SELECT * FROM Tokens WHERE token = :token';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':token', $token);
            $prepared->execute();

            if ($prepared->rowCount() === 1) {
                $result = $prepared->fetch(PDO::FETCH_ASSOC);
                
                $token = new Token();
                $token->set_user($result['id']);
                $token->set_token($result['token']);
                $token->set_tmp_file_name($result['cookie_file']);
                $token->set_expires($result['expires']);

                return $token;
            }

            return false;
        }

        function insert_new_user(int $id, int $first_term, int $last_term) {
            $query = 'INSERT INTO Users VALUES (:id, :first_term, :last_term, :last_login)';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->bindParam(':first_term', $first_term);
            $prepared->bindParam(':last_term', $last_term);
            $prepared->bindValue(':last_login', time());
            $prepared->execute();
        }

        function insert_new_token(int $id, Token $token) {
            $query = 'INSERT INTO Tokens VALUES (:id, :token, :tmp_file, :expire)';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->bindValue(':token', $token->get_token());
            $prepared->bindValue(':tmp_file', $token->get_tmp_file_name());
            $prepared->bindValue(':expire', $token->get_expires());
            $prepared->execute();
        }

        function update_user(int $id, int $last_term) {
            $query = 'UPDATE Users SET last_login = :last_login, last_term = :last_term WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->bindValue(':last_login', time());
            $prepared->bindValue(':last_term', $last_term);
            $prepared->execute();
        }

        function update_token_token(int $id, string $new_token) {
            $query = 'UPDATE Tokens SET token = :token WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->bindValue(':token', $new_token);
            $prepared->execute();
        }

        function delete_token($id) {
            $query = 'DELETE FROM Tokens WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->execute();
        }
    }
