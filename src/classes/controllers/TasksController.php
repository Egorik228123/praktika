<?php
    require_once __DIR__ . "/../contexts/tasksContext.php";
    require_once __DIR__ . "/../DB.php"; // Убедитесь, что DB.php подключен, если TasksContext его использует
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

        public function updateTask(int $taskId, array $taskData): array {
            try {
                if ($taskId <= 0) {
                    $this->addError("Некорректный ID задачи");
                    return ['success' => false, 'errors' => $this->errors];
                }
                if (empty($taskData['due_date'])) {
                    $taskData['due_date'] = null;
                }
                if (empty($taskData['description'])) {
                    $taskData['description'] = null;
                }
                
                $this->tasksContext->updateTask($taskId, [
                    'name' => $taskData['name'] ?? null,
                    'description' => $taskData['description'] ?? null,
                    'due_date' => $taskData['due_date'] ?? null
                ]);

                // Обработка ответственных
                $assignees = json_decode($_POST['assignees'] ?? '[]', true);
                $this->tasksContext->updateAssignees($taskId, $assignees);

                // Обработка подзадач
                $subtasks = json_decode($_POST['subtasks'] ?? '[]', true);
                $this->tasksContext->updateSubtasks($taskId, $subtasks);

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

        public function getTaskDetails(int $taskId): array {
            try {
                $task = $this->tasksContext->getTaskWithDetails($taskId);
                $assignees = $this->tasksContext->getTaskAssignees($taskId);
                $subtasks = $this->tasksContext->getTaskSubtasks($taskId);
                
                return [
                    'success' => true,
                    'data' => [
                        'task' => $task,
                        'assignees' => $assignees,
                        'subtasks' => $subtasks
                    ]
                ];
            } catch (Exception $e) {
                error_log("Ошибка получения деталей задачи: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function createTask(array $taskData): array {
            try {
                if (empty($taskData['due_date'])) {
                    $taskData['due_date'] = null;
                }
                if (empty($taskData['description'])) {
                    $taskData['description'] = null;
                }
                
                $taskId = $this->tasksContext->createTask($taskData);
        
                // Добавляем ответственных
                if (isset($taskData['assignees'])) {
                    $assignees = json_decode($taskData['assignees'], true);
                    foreach ($assignees as $userId) {
                        $this->tasksContext->addAssignee($taskId, $userId);
                    }
                }
                
                return ['success' => true, 'data' => ['task_id' => $taskId]];
            } catch (Exception $e) {
                error_log("Ошибка создания задачи: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Добавление ответственного
        public function addAssignee(int $taskId, int $userId): array {
            try {
                $this->tasksContext->addAssignee($taskId, $userId);
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Удаление ответственного
        public function removeAssignee(int $taskId, int $userId): array {
            try {
                $this->tasksContext->removeAssignee($taskId, $userId);
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Создание подзадачи
        public function createSubtask(array $subtaskData): array {
            try {
                $subtaskId = $this->tasksContext->createSubtask($subtaskData);
                return ['success' => true, 'data' => ['subtask_id' => $subtaskId]];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Удаление подзадачи
        public function deleteSubtask(int $subtaskId): array {
            try {
                $this->tasksContext->deleteSubtask($subtaskId);
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Перемещение всех задач между столбцами
        public function moveAllTasks(int $fromColumnId, int $toColumnId): array {
            try {
                $this->tasksContext->moveAllTasks($fromColumnId, $toColumnId);
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Обработчик запросов
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
                            'column_id' => $_POST['column_id'],
                            'assignees' => $_POST['assignees'] ?? '[]' // Передача assignees
                        ]);
                        break;
                    case 'updateTask':
                        $response = $controller->updateTask(
                            (int)$_POST['task_id'], 
                            [
                                'name' => $_POST['name'] ?? null,
                                'description' => $_POST['description'] ?? null,
                                'due_date' => $_POST['due_date'] ?? null
                            ]
                        );
                        break;
                    case 'deleteTask':
                        $response = $controller->deleteTask((int)$_POST['task_id']);
                        break;
                    case 'moveTask':
                        $response = $controller->moveTask(
                            (int)$_POST['task_id'],
                            (int)$_POST['new_column_id']
                        );
                        break;
                    case 'assignUser':
                        $response = $controller->assignUser((int)$_POST['task_id'], (int)$_POST['user_id']);
                        break;
                    case 'getTasksByColumn':
                        $response = $controller->getTasksByColumn((int)$_POST['column_id']);
                        break;
                    case 'getTaskDetails':
                        $response = $controller->getTaskDetails((int)$_POST['task_id']);
                        break;
                    case 'addAssignee':
                        $response = $controller->addAssignee((int)$_POST['task_id'], (int)$_POST['user_id']);
                        break;
                    case 'removeAssignee':
                        $response = $controller->removeAssignee((int)$_POST['task_id'], (int)$_POST['user_id']);
                        break;
                    case 'createSubtask':
                        $response = $controller->createSubtask([
                            'name' => $_POST['name'],
                            'description' => $_POST['description'] ?? null,
                            'task_id' => (int)$_POST['task_id']
                        ]);
                        break;
                    case 'deleteSubtask':
                        $response = $controller->deleteSubtask((int)$_POST['subtask_id']);
                        break;
                    case 'moveAllTasks':
                        $response = $controller->moveAllTasks((int)$_POST['from_column_id'], (int)$_POST['to_column_id']);
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