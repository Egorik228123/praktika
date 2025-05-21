<?php
    require_once __DIR__ . "../models/Users.php";
    require_once __DIR__ . "../class_db.php";

    class UsersContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        public function UpdateUser(int $userId, array $fields): void {
            try {
                $allowedFields = ['name', 'surname', 'middlename', 'email', 'password', 'bio'];
                $updates = [];
                $params = [];
                
                foreach ($fields as $key => $value) {
                    if (!in_array($key, $allowedFields)) continue;
                    if ($key === 'password') {
                        $value = password_hash($value, PASSWORD_DEFAULT);
                    }
                    $updates[] = "`$key` = ?";
                    $params[] = $value;
                }
                
                if (empty($updates)) {
                    throw new Exception("Нет полей для обновления");
                }
                
                $sql = "UPDATE `users` SET " . implode(', ', $updates) . " WHERE `id` = ?";
                $params[] = $userId;
                $this->db->QueryExecute($sql, $params);
            }
            catch(mysqli_sql_exception $e) {
                throw new mysqli_sql_exception("Ошибка запроса: " . $e->getMessage(), 0, $e);
            }
        }

        public function DeleteUser(int $userId): int|string {
            try {
                $affected_rows = $this->db->QueryExecute(
                    "DELETE FROM `users` WHERE `id` = ?",
                    [$userId]
                );
                return $affected_rows;
            }
            catch(mysqli_sql_exception $e) {
                throw new mysqli_sql_exception("Ошибка запроса: " . $e->getMessage(), 0, $e);
            }
        }

        public function GetById(int $userId): ?Users {
            try {
                $result = $this->db->Query(
                    "SELECT * FROM `users` WHERE `id` = ?",
                    [$userId]
                );

                if ($data = $result->fetch_object()) {
                    return new Users($data);
                }
                return null;
            }
            catch(mysqli_sql_exception $e) {
                throw new mysqli_sql_exception("Ошибка запроса: " . $e->getMessage(), 0, $e);
            }
        }

        public function GetFIO(int $userId): ?array {
            try {
                $result = $this->db->Query(
                    "SELECT name, surname, middlename FROM users WHERE id = ?",
                    [$userId]
                );
                return $result->fetch_assoc() ?? null;
            }
            catch(mysqli_sql_exception $e) {
                throw new Exception("Ошибка получения ФИО: " . $e->getMessage());
            }
        }

        public function GetAllUsers(): array {
            try {
                $result = $this->db->Query("SELECT * FROM users");
                return $result->fetch_all(MYSQLI_ASSOC);
            }
            catch(mysqli_sql_exception $e) {
                throw new Exception("Ошибка загрузки пользователей: " . $e->getMessage());
            }
        }

        public function GetAllProjects(int $userId): array {
            try {
                $result = $this->db->Query(
                    "SELECT p.* FROM projects p
                    INNER JOIN project_roles pr ON p.id = pr.project_id
                    WHERE pr.user_id = ?",
                    [$userId]
                );
                return $result->fetch_all(MYSQLI_ASSOC);
            }
            catch(mysqli_sql_exception $e) {
                throw new Exception("Ошибка загрузки проектов: " . $e->getMessage());
            }
        }

        public function Authorize(string $email, string $password): ?Users {
            try {
                $result = $this->db->Query(
                    "SELECT * FROM users WHERE email = ?",
                    [$email]
                );
                $userData = $result->fetch_assoc();

                if (!$userData || !password_verify($password, $userData['password'])) {
                    return null;
                }

                return new Users($userData);
            }
            catch(mysqli_sql_exception $e) {
                throw new Exception("Ошибка авторизации: " . $e->getMessage());
            }
        }

        public function Register(array $userData) {
            $result = $this->db->Query(
                "SELECT id FROM users WHERE email = ?",
                [ $userData['email'] ]
            );
            if ($result->fetch_assoc()) {
                throw new Exception("Пользователь с таким email уже существует");
            }

            $sql = "INSERT INTO users (name, surname, middlename, email, password) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $this->db->QueryExecute($sql, [
                $userData['name'],
                $userData['surname'],
                $userData['middlename'] ?? null,
                $userData['email'],
                password_hash($userData['password'], PASSWORD_DEFAULT),
            ]);

            $userId = $this->db->lastInsertId();
            return $this->GetById($userId);
        }
    }
?>