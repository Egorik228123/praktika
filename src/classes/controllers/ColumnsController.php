<?php
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . "/../contexts/ColumnsContext.php";

    class ColumnsController {
        private ColumnsContext $columnsContext;
        public array $errors = [];

        public function __construct() {
            $db = new DBConnect();
            $this->columnsContext = new ColumnsContext($db);
        }

        private function addError(string $message): void {
            $this->errors[] = $message;
        }

        // Создание столбца
        public function createColumn(array $columnData): array {
            try {
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

        // Удаление столбца
        public function deleteColumn(int $columnId): array {
            try {
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

        // Обработчик запросов
        public static function handleRequest() {
            $controller = new self();
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'createColumn':
                        $response = $controller->createColumn([
                            'name' => $_POST['name'],
                            'position' => $_POST['position'],
                            'project_id' => $_POST['project_id'],
                        ]);
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
                        $response = $controller->deleteColumn($_POST['column_id']);
                        break;

                    case 'getColumnsByProject':
                        $response = $controller->getColumnsByProject($_POST['project_id']);
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