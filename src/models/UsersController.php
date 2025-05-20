<?php
    header('Content-Type: application/json');
    require_once "/domains/localhost/praktika-models/models/usersContext.php";

    class UsersController {
        private UsersContext $usersContext;

        public function __construct() {
            $db = new DBConnect();
            $this->usersContext = new UsersContext($db);
        }

        public function deleteUser(int $userId): void {
            $affectedRows = $this->usersContext->Delete($userId);
            echo json_encode([
                'success' => $affectedRows > 0,
                'message' => $affectedRows > 0 ? "User deleted" : "User not found"
            ]);
        }

        public function updateUser(int $userId, array $userData): void {
            $fields = array_filter(
                $userData, 
                fn($key) => in_array($key, [
                'name', 'surname', 'middlename', 'email', 'password', 'bio'
            ]), ARRAY_FILTER_USE_KEY);

            if(isset($fields['password'])) {
                $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);
            }

            $this->usersContext->Update($userId, $fields);
            echo json_encode([
                'success' => true,
                'message' => "User updated"
            ]);
        }

        public function insertUser(array $userData): void {
            $required = ['name', 'email', 'password'];
            foreach($required as $field) {
                if(empty($userData[$field])) {
                    throw new InvalidArgumentException("Missing required field: $field");
                }
            }

            $user = new Users([
                'name' => $userData['name'],
                'surname' => $userData['surname'] ?? '',
                'middlename' => $userData['middlename'] ?? '',
                'email' => $userData['email'],
                'password' => $userData['password'],
                'bio' => $userData['bio'] ?? ''
            ]);

            $this->usersContext->Insert($user);
            echo json_encode([
                'success' => true,
                'message' => "User created"
            ]);
        }

        public function getUser(int $userId): void {
            $user = $this->usersContext->GetById($userId);
            if($user) {
                echo json_encode([
                    'success' => true, 
                    'data' => $user->toArray()
                ]);
            }
            else {
                echo json_encode([
                    'success' => false, 
                    'error' => 'User not found'
                ]);
            }
        }

        public function getAllUsers(): void {
            $users = $this->usersContext->GetAll();
            echo json_encode([
                'success' => true, 
                'data' => array_map(fn($u) => $u->toArray(), $users)
            ]);
        }
    }
?>