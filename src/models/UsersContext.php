<?php
    require_once "/domains/localhost/praktika-models/models/users.php";
    require_once "/domains/localhost/praktika-models/models/class_db.php";

    class UsersContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        // Добавление пользователя
        public function Insert(Users $user): int|string {
            $affected_rows = $this->db->QueryExecute(
                "INSERT INTO `users` (`name`, `surname`, `middlename`, `email`, `password`, `bio`) 
                VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $user->name,
                    $user->surname,
                    $user->middlename,
                    $user->email,
                    password_hash($user->password, PASSWORD_DEFAULT),
                    $user->bio,
                ]
            );
            return $affected_rows;
        }

        // Обновление пользователя (частичное или полное)
        public function Update(int $userId, array $fields): void {
            if (empty($fields)) {
                throw new InvalidArgumentException("No fields to update.");
            }

            $setClause = [];
            $params = [];
            foreach ($fields as $key => $value) {
                $setClause[] = "`$key` = ?";
                $params[] = $value;
            }
            $params[] = $userId;

            $query = "UPDATE `users` SET " . implode(', ', $setClause) . " WHERE `id` = ?";
            $this->db->QueryExecute($query, $params);
        }

        // Удаление пользователя
        public function Delete(int $userId): int|string {
            $affected_rows = $this->db->QueryExecute(
                "DELETE FROM `users` WHERE `id` = ?",
                [$userId]
            );
            return $affected_rows;
        }

        // Получение пользователя по ID
        public function GetById(int $userId): ?Users {
            $result = $this->db->Query(
                "SELECT * FROM `users` WHERE `id` = ?",
                [$userId]
            );
            
            if ($data = $result->fetch_object()) {
                return new Users($data);
            }
            return null;
        }

        public function GetAll(): array {
            $result = $this->db->Query("SELECT * FROM `users`");
            $users = [];
            while($data = $result->fetch_object()) {
                $users[] = new Users($data);
            }
            return $users;
        }
    }
?>