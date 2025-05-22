<?php
    require_once __DIR__ . "/../models/Users.php";
    require_once __DIR__ . "/../DB.php";

    class UsersContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        public function updateUser(int $userId, array $fields): void {
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

        public function deleteUser(int $userId): int|string {
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

        public function getById(int $userId): ?Users {
            try {
                $result = $this->db->Query(
                    "SELECT `id`, `name`, `surname`, `middlename`, `email`, `bio` FROM `users` WHERE `id` = ?",
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

        public function getFIO(int $userId): ?array {
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

        public function getAllUsers(): array {
            try {
                $result = $this->db->Query("SELECT * FROM users");
                return $result->fetch_all(MYSQLI_ASSOC);
            }
            catch(mysqli_sql_exception $e) {
                throw new Exception("Ошибка загрузки пользователей: " . $e->getMessage());
            }
        }

        public function getAllProjects(int $userId): array {
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

        public function authorize(string $email, string $password): ?Users {
            try {
                $result = $this->db->Query(
                    "SELECT id, email, password FROM users WHERE email = ?",
                    [$email]
                );
                $userData = $result->fetch_assoc();

                if (!$userData || !password_verify($password, $userData['password'])) {
                    return null;
                }

                $userData = $this->getById($userData['id']);
                return new Users($userData);
            }
            catch(mysqli_sql_exception $e) {
                throw new Exception("Ошибка авторизации: " . $e->getMessage());
            }
        }

        public function register(array $userData) {
            try {
                // Проверка существующего email
                $result = $this->db->Query(
                    "SELECT id FROM users WHERE email = ? LIMIT 1",
                    [$userData['email']]
                );
                if ($result->fetch_assoc()) {
                    throw new Exception("Пользователь с таким email уже существует");
                }

                // Хеширование пароля
                $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);

                // Вставка пользователя
                $sql = "INSERT INTO users 
                        (name, surname, middlename, email, password, bio)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $affectedRows = $this->db->QueryExecute($sql, [
                    $userData['name'],
                    $userData['surname'],
                    $userData['middlename'] ?? null,
                    $userData['email'],
                    $passwordHash,
                    $userData['bio'] ?? null
                ]);

                // Проверка успешности вставки
                if ($affectedRows === 0) {
                    throw new Exception("Ошибка создания пользователя");
                }

                // Получение созданного пользователя
                $userId = $this->db->lastInsertId();
                $newUser = $this->GetById($userId);
                if (!$newUser) {
                    throw new Exception("Ошибка получения данных пользователя");
                }

                return $newUser;

                }
            catch (mysqli_sql_exception $e) {
                throw new Exception("Ошибка регистрации: " . $e->getMessage());
            }
        }
    }
?>