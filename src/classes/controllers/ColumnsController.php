<?php
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . "/../contexts/ProjectsContext.php";
    require_once __DIR__ . "/../contexts/ColumnsContext.php";
    require_once __DIR__ . "/../contexts/TasksContext.php";

    class ColumnsController {
        private ColumnsContext $columnsContext;
        private TasksContext $tasksContext;
        private ProjectsContext $projectsContext;

        public array $errors = [];

        public function __construct() {
            $db = new DBConnect();
            $this->columnsContext = new ColumnsContext($db);
            $this->tasksContext = new TasksContext($db);
            $this->projectsContext = new ProjectsContext($db);
        }

        private function addError(string $message): void {
            $this->errors[] = $message;
        }

        // Создание столбца
        public function createColumn(array $columnData, int $userId): array {
            try {
                $role = $this->projectsContext->getUserRoleInProject($userId, $columnData['project_id']);
                if (!in_array($role, ['creator', 'admin'])) {
                    return ['success' => false, 'errors' => ['У вас нет прав для создания столбцов.']];
                }
                
                $required = ['name', 'position', 'project_id'];
                foreach ($required as $field) {
                    if (empty($columnData[$field])) {
                        $this->addError("Поле $field обязательно");
                    }
                }

                if (!empty($this->errors)) {
                    return ['success' => false, 'errors' => $this->errors];
                }

                $columnId = $this->columnsContext->createColumn($columnData);
                return ['success' => true, 'data' => ['column_id' => $columnId]];
            } catch (Exception $e) {
                error_log("Ошибка создания: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Обновление столбца
        public function updateColumn(int $columnId, array $columnData): array {
            try {
                if ($columnId <= 0) {
                    $this->addError("Некорректный ID столбца");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $this->columnsContext->updateColumn($columnId, $columnData);
                return ['success' => true];
            } catch (Exception $e) {
                error_log("Ошибка обновления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Удаление столбца с перемещением задач
        public function deleteColumn(int $columnId, int $projectId, int $userId): array {
            try {
                $role = $this->projectsContext->getUserRoleInProject($userId, $projectId);
                if (!in_array($role, ['creator', 'admin'])) {
                    return ['success' => false, 'errors' => ['У вас нет прав для удаления столбцов.']];
                }
                
                $columns = $this->columnsContext->getColumnsByProject($projectId);
                
                $firstColumnId = null;
                foreach ($columns as $column) {
                    if ($column['id'] != $columnId) {
                        $firstColumnId = $column['id'];
                        break;
                    }
                }
                
                if ($firstColumnId) {
                    $this->tasksContext->moveAllTasks($columnId, $firstColumnId);
                } else {
                    $this->tasksContext->deleteTasksByColumn($columnId);
                }

                $this->columnsContext->deleteColumn($columnId);
                return ['success' => true];
            } catch (Exception $e) {
                error_log("Ошибка удаления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Получение столбцов проекта
        public function getColumnsByProject(int $projectId): array {
            try {
                $columns = $this->columnsContext->getColumnsByProject($projectId);
                return ['success' => true, 'data' => $columns];
            } catch (Exception $e) {
                error_log("Ошибка получения: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public static function handleRequest() {
            session_start();
            $controller = new self();
            $action = $_POST['action'] ?? '';
            $userId = $_SESSION['user']['id'] ?? 0;

            try {
                switch ($action) {
                    case 'createColumn':
                        $response = $controller->createColumn([
                            'name' => $_POST['name'],
                            'position' => $_POST['position'],
                            'project_id' => $_POST['project_id'],
                        ], $userId);
                        break;
                    case 'updateColumn':
                        $response = $controller->updateColumn(
                            $_POST['column_id'],
                            [
                                'name' => $_POST['name'] ?? null,
                                'position' => $_POST['position'] ?? null
                            ]
                        );
                        break;
                    case 'deleteColumn':
                        $response = $controller->deleteColumn(
                            (int)$_POST['column_id'],
                            (int)$_POST['project_id'],
                            $userId
                        );
                        break;
                    case 'getColumnsByProject':
                        $response = $controller->getColumnsByProject((int)$_POST['project_id']);
                        break;
                    default:
                        $response = ['success' => false, 'errors' => ['Неверное действие']];
                }
            } catch (Exception $e) {
                $response = ['success' => false, 'errors' => [$e->getMessage()]];
            }

            echo json_encode($response);
            exit();
        }
    }
    ColumnsController::handleRequest();
?>