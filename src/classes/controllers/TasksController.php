<?php
    require_once __DIR__ . "/../contexts/TasksContext.php";
    header('Content-Type: application/json; charset=utf-8');
    
    class TasksController {
        private TasksContext $tasksContext;
        public array $errors = [];

        public function __construct() {
            $db = new DBConnect();
            $this->tasksContext = new TasksContext($db);
        }

        private function addError(string $message): void {
            $this->errors[] = $message;
        }

        public function createTask(array $taskData): array {
            try {
                if (empty($taskData['name']) || empty($taskData['column_id'])) {
                    $this->addError("Обязательные поля: название и столбец");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $taskId = $this->tasksContext->createTask($taskData);
                return ['success' => true, 'data' => ['task_id' => $taskId]];
            }
            catch (Exception $e) {
                error_log("Ошибка создания задачи: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function updateTask(int $taskId, array $taskData): array {
            try {
                if ($taskId <= 0) {
                    $this->addError("Некорректный ID задачи");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $this->tasksContext->updateTask($taskId, $taskData);
                return ['success' => true];
            }
            catch (Exception $e) {
                error_log("Ошибка обновления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function deleteTask(int $taskId): array {
            try {
                if ($taskId <= 0) {
                    $this->addError("Некорректный ID задачи");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $this->tasksContext->deleteTask($taskId);
                return ['success' => true];
            }
            catch (Exception $e) {
                error_log("Ошибка удаления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function moveTask(int $taskId, int $newColumnId): array {
            try {
                $this->tasksContext->moveTask($taskId, $newColumnId);
                return ['success' => true];
            }
            catch (Exception $e) {
                error_log("Ошибка перемещения: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function assignUser(int $taskId, int $userId): array {
            try {
                $this->tasksContext->assignUser($taskId, $userId);
                return ['success' => true];
            }
            catch (Exception $e) {
                error_log("Ошибка назначения: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function getTasksByColumn(int $columnId): array {
            try {
                $tasks = $this->tasksContext->getTasksByColumn($columnId);
                return ['success' => true, 'data' => $tasks];
            }
            catch (Exception $e) {
                error_log("Ошибка получения: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public static function handleRequest() {
            $controller = new self();
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'createTask':
                        $response = $controller->createTask([
                            'name' => $_POST['name'],
                            'description' => $_POST['description'] ?? null,
                            'due_date' => $_POST['due_date'] ?? null,
                            'column_id' => $_POST['column_id']
                        ]);
                        break;
                    case 'updateTask':
                        $response = $controller->updateTask(
                            $_POST['task_id'], [
                                'name' => $_POST['name'] ?? null,
                                'description' => $_POST['description'] ?? null,
                                'due_date' => $_POST['due_date'] ?? null
                            ]
                        );
                        break;
                    case 'deleteTask':
                        $response = $controller->deleteTask($_POST['task_id']);
                        break;
                    case 'moveTask':
                        $response = $controller->moveTask(
                            $_POST['task_id'],
                            $_POST['new_column_id']
                        );
                        break;
                    case 'assignUser':
                        $response = $controller->assignUser($_POST['task_id'], $_POST['user_id']);
                        break;
                    case 'getTasksByColumn':
                        $response = $controller->getTasksByColumn($_POST['column_id']);
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
    TasksController::handleRequest();
?>