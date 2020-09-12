<?php
    require_once('IDatabase.php');
    require_once(__DIR__ . '/../object/User.php');
    require_once(__DIR__ . '/../object/Token.php');

    class SQLite implements IDatabase {
        private $db;

        public function __construct($file) {
            $this->db = new PDO("sqlite:$file");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec('PRAGMA foreign_keys = ON;');

            $this->create_user_table();
            $this->create_token_table();
        }

        private function create_user_table() {
            $this->db->exec('
                CREATE TABLE IF NOT EXISTS Users (
                    id INTEGER PRIMARY KEY,
                    first_term INTEGER NOT NULL,
                    last_login INTEGER NOT NULL
                )
            ');
        }

        private function create_token_table() {
            $this->db->exec('
                CREATE TABLE IF NOT EXISTS Tokens (
                    id INTEGER UNIQUE,
                    token TEXT NOT NULL UNIQUE,
                    cookie_file TEXT NOT NULL UNIQUE,
                    expires INTEGER NOT NULL,
                    FOREIGN KEY (id) REFERENCES Users(id) ON DELETE CASCADE
                )
            ');
        }

        function get_user(int $id) {
            $query = 'SELECT * FROM Users WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->execute();

            $result = $prepared->fetch(PDO::FETCH_ASSOC);
            if ($result !== false) {
                return new User($result['id'], $result['last_login'], $result['first_term']);
            }

            return false;
        }

        function get_token_by_id(int $id) {
            $query = 'SELECT * FROM Tokens WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->execute();

            $result = $prepared->fetch(PDO::FETCH_ASSOC);
            if ($result !== false) {
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

            $result = $prepared->fetch(PDO::FETCH_ASSOC);
            if ($result !== false) {
                $token = new Token();
                $token->set_user($result['id']);
                $token->set_token($result['token']);
                $token->set_tmp_file_name($result['cookie_file']);
                $token->set_expires($result['expires']);

                return $token;
            }

            return false;
        }

        function insert_new_user(int $id, int $first_term) {
            $query = 'INSERT INTO Users VALUES (:id, :first_term, :last_login)';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->bindParam(':first_term', $first_term);
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

        function update_user_last_login(int $id) {
            $query = 'UPDATE Users SET last_login = :last_login WHERE id = :id';

            $prepared = $this->db->prepare($query);
            $prepared->bindParam(':id', $id);
            $prepared->bindValue(':last_login', time());
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
