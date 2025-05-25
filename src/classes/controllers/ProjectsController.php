<?php
    require_once __DIR__ . "/../contexts/ProjectsContext.php";
    header('Content-Type: application/json; charset=utf-8');
    class ProjectsController {
        private ProjectsContext $projectsContext;
        public array $errors = [];

        public function __construct() {
            $db = new DBConnect();
            $this->projectsContext = new ProjectsContext($db);
        }

        private function addError(string $message): void {
            $this->errors[] = $message;
        }

        // Создание проекта
        public function createProject(array $projectData): array {
            try {
                if (empty($projectData['name'])) {
                    $this->addError("Название проекта обязательно");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $projectId = $this->projectsContext->createProject($projectData);
                return ['success' => true, 'data' => ['project_id' => $projectId]];
            } catch (Exception $e) {
                error_log("Ошибка создания: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Обновление проекта
        public function updateProject(int $projectId, array $projectData): array {
            try {
                if ($projectId <= 0) {
                    $this->addError("Некорректный ID проекта");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $this->projectsContext->updateProject($projectId, $projectData);
                return ['success' => true];
            } catch (Exception $e) {
                error_log("Ошибка обновления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Удаление проекта
        public function deleteProject(int $projectId): array {
            try {
                $this->projectsContext->deleteProject($projectId);
                return ['success' => true];
            } catch (Exception $e) {
                error_log("Ошибка удаления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Добавление участника
        public function addMember(int $projectId, int $userId, string $role): array {
            try {
                if (!in_array($role, ['creator', 'admin', 'user'])) {
                    $this->addError("Некорректная роль");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $this->projectsContext->addMember($projectId, $userId, $role);
                return ['success' => true];
            } catch (Exception $e) {
                error_log("Ошибка добавления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Получение участников
        public function getMembers(int $projectId): array {
            try {
                $members = $this->projectsContext->getMembers($projectId);
                return ['success' => true, 'data' => $members];
            }
            catch (Exception $e) {
                error_log("Ошибка получения: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        // Получение публичных проектов
        public function getPublicProjects(): array {
            try {
                $projects = $this->projectsContext->getPublicProjects();
                return ['success' => true, 'data' => $projects];
            }
            catch (Exception $e) {
                error_log("Ошибка загрузки: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function getProjectById(int $projectId): array {
            try {
                $projects = $this->projectsContext->getProjectById($projectId);
                return ['success' => true, 'data' => $projects];
            }
            catch (Exception $e) {
                error_log("Ошибка получения проекта: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public static function handleRequest() {
            $controller = new self();
            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'getPublicProjects':
                        $response = $controller->getPublicProjects();
                        break;
                    case 'getProjectById':
                        $response = $controller->getProjectById($_POST['id']);
                        break;
                    case 'createProject':
                        $response = $controller->createProject($_POST);
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
    ProjectsController::handleRequest();
?>