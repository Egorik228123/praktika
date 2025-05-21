<?php
    require_once __DIR__ . "/../contexts/UsersContext.php";

    class UsersController {
        private UsersContext $usersContext;
        public array $errors = [];

        public function __construct() {
            $db = new DBConnect();
            $this->usersContext = new UsersContext($db);
        }

        private function addError(string $message): void {
            $this->errors[] = $message;
        }
        
        public function DeleteUser(int $userId) {
            try {
                if ($userId <= 0) {
                    $this->addError("Некорректный ID пользователя");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $user = $this->usersContext->GetById($userId);
                if (!$user) {
                    $this->addError("Пользователь не найден");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $result = $this->usersContext->DeleteUser($userId);
                return ['success' => true, 'data' => $result];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера при удалении: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }

        }

        public function UpdateUser(int $userId, array $userData) {
            try {
                if ($userId <= 0) {
                    $this->addError("Некорректный ID");
                    return ['success' => false, 'errors' => $this->errors];
                }

                if (empty($userData)) {
                    $this->addError("Нет данных для обновления");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $user = $this->usersContext->GetById($userId);
                if (!$user) {
                    $this->addError("Пользователь не найден");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $this->usersContext->UpdateUser($userId, $userData);
                return ['success' => true];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера при обновлении: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }
        }

        public function GetUserById(int $userId) {
            try {
                if ($userId <= 0) {
                    $this->addError("Некорректный ID");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $user = $this->usersContext->GetById($userId);
                if (!$user) {
                    $this->addError("Пользователь не найден");
                    return ['success' => false, 'errors' => $this->errors];
                }

                return ['success' => true, 'data' => $user];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }
        }

        public function GetUserFIO(int $userId): array {
            try {
                if ($userId <= 0) {
                    $this->addError("Некорректный ID");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $fio = $this->usersContext->GetFIO($userId);
                if (!$fio) {
                    $this->addError("Данные не найдены");
                    return ['success' => false, 'errors' => $this->errors];
                }

                return ['success' => true, 'data' => $fio];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }
        }

        public function GetAllUsers(): array {
            try {
                $users = $this->usersContext->GetAllUsers();
                return ['success' => true, 'data' => $users];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка загрузки списка']];
            }   
        }

        public function GetUserProjects(int $userId): array {
            try {
                if ($userId <= 0) {
                    $this->addError("Некорректный ID");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $projects = $this->usersContext->GetAllProjects($userId);
                return ['success' => true, 'data' => $projects];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка загрузки проектов']];
            }
        }

        public function AuthorizeUser(string $email, string $password): array {
            try {
                if (empty($email) || empty($password)) {
                    $this->addError("Заполните email и пароль");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $user = $this->usersContext->Authorize($email, $password);
                if (!$user) {
                    $this->addError("Неверные учетные данные");
                    return ['success' => false, 'errors' => $this->errors];
                }

                return ['success' => true, 'data' => $user];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка авторизации']];
            }
        }

        // Регистрация
        public function RegisterUser(array $userData) {
            try {
                $required = ['name', 'surname', 'email', 'password'];
                foreach ($required as $field) {
                    if (empty($userData[$field])) {
                        $this->addError("Поле $field обязательно");
                    }
                }

                if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->addError("Некорректный email");
                }

                if (!empty($this->errors)) {
                    return ['success' => false, 'errors' => $this->errors];
                }

                $newUser = $this->usersContext->Register($userData);
                return ['success' => true, 'data' => $newUser];
            }
            catch(Exception $e) {
                if ($e->getMessage() === "Пользователь с таким email уже существует") {
                    $this->addError($e->getMessage());
                    return ['success' => false, 'errors' => $this->errors];
                }
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }
        }
    }
?>