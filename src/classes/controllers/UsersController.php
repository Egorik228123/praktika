<?php
    header('Content-Type: application/json; charset=utf-8');
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
        
        public function deleteUser(int $userId) {
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

                $result = $this->usersContext->deleteUser($userId);
                return ['success' => true, 'data' => $result];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера при удалении: " . $e->getMessage(), 3, "../../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }

        }

        public function updateUser(int $userId, array $userData) {
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
                error_log("Ошибка сервера при обновлении: " . $e->getMessage(), 3, "../../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }
        }

        public function getUserById(int $userId) {
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
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }
        }

        public function getAllUsers(): array {
            try {
                $users = $this->usersContext->GetAllUsers();
                return ['success' => true, 'data' => $users];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../../error.log");
                return ['success' => false, 'errors' => ['Ошибка загрузки списка']];
            }   
        }

        public function authorizeUser(string $email, string $password): array {
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

                session_start();
                $_SESSION['user'] = [
                    'id' => $user->id,
                ];

                return ['success' => true, 'data' => $user];
            }
            catch(Exception $e) {
                error_log("Ошибка сервера: " . $e->getMessage() . "\n", 3, "../../error.log");
                return ['success' => false, 'errors' => ['Ошибка авторизации']];
            }
        }

        public function registerUser(array $userData) {
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
                error_log("Ошибка сервера: " . $e->getMessage(), 3, "../../error.log");
                return ['success' => false, 'errors' => ['Ошибка сервера']];
            }
        }

        public static function handleRequest() {
            $controller = new self();
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'authorize':
                        $response = $controller->authorizeUser($_POST['email'], $_POST['password']);
                        break;
                    case 'register':
                        $response = $controller->registerUser($_POST);
                        break;
                    case 'getUserById':
                        $response = $controller->getUserById($_POST['id']);
                        break;
                    case 'getAllUsers':
                        $response = $controller->getAllUsers();
                        break;
                    default:
                        $response = ['success' => false, 'errors' => ['Неверное действие']];
                }
            }
            catch (Exception $e) {
                $response = ['success' => false, 'errors' => [$e->getMessage()]];
            }

            echo json_encode($response);
            exit();
        }
    }
    UsersController::handleRequest();
?>