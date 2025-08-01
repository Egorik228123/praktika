<?php
    require_once __DIR__ . "/../contexts/ProjectsContext.php";
    require_once __DIR__ . "/../DB.php";
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

        public function createProject(array $projectData): array {
            try {
                if (empty($projectData['name'])) {
                    $this->addError("Название проекта обязательно");
                    return ['success' => false, 'errors' => $this->errors];
                }
                $projectData['is_public'] = isset($projectData['is_public']) ? (int)$projectData['is_public'] : 0;

                $projectId = $this->projectsContext->createProject($projectData);
                return ['success' => true, 'data' => ['project_id' => $projectId]];
            } catch (Exception $e) {
                error_log("Ошибка создания: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function updateProject(int $projectId, array $projectData): array {
            try {
                if ($projectId <= 0) {
                    $this->addError("Некорректный ID проекта");
                    return ['success' => false, 'errors' => $this->errors];
                }
                if (isset($projectData['is_public'])) {
                    $projectData['is_public'] = (int)$projectData['is_public'];
                }

                $this->projectsContext->updateProject($projectId, $projectData);
                return ['success' => true];
            } catch (Exception $e) {
                error_log("Ошибка обновления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function deleteProject(int $projectId, int $userId): array {
            try {
                // Проверка, является ли пользователь создателем проекта
                $creator = $this->projectsContext->getProjectCreator($projectId);
                if ($creator['id'] !== $userId) {
                    $this->addError("У вас нет прав для удаления этого проекта.");
                    return ['success' => false, 'errors' => $this->errors];
                }

                $this->projectsContext->deleteProject($projectId);
                return ['success' => true];
            } catch (Exception $e) {
                error_log("Ошибка удаления: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

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

        public function getAllUserProjects(int $userId): array {
            try {
                $projects = $this->projectsContext->getAllProjectsForUser($userId);
                return ['success' => true, 'data' => $projects];
            } catch (Exception $e) {
                 error_log("Ошибка загрузки проектов пользователя: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }


        public function getProjectById(int $projectId): array {
            try {
                $project = $this->projectsContext->getProjectById($projectId);
                return ['success' => true, 'data' => $project];
            }
            catch (Exception $e) {
                error_log("Ошибка получения проекта: " . $e->getMessage());
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function addProjectMember(int $projectId, int $userId): array {
            try {
                $this->projectsContext->addMember($projectId, $userId, 'user');
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function getProjectMembers(int $projectId): array {
            try {
                $members = $this->projectsContext->getProjectMembers($projectId);
                return ['success' => true, 'data' => $members];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function removeMember(int $projectId, int $userId): array {
            try {
                $this->projectsContext->removeMember($projectId, $userId);
                return ['success' => true];
            } catch (Exception $e) {
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
                    case 'getAllUserProjects':
                        $response = $controller->getAllUserProjects((int)$_POST['user_id']);
                        break;
                    case 'getProjectById':
                        $response = $controller->getProjectById((int)$_POST['id']);
                        break;
                    case 'createProject':
                        $response = $controller->createProject($_POST);
                        break;
                    case 'updateProject':
                        $response = $controller->updateProject(
                            (int)$_POST['project_id'],
                            [
                                'name' => $_POST['name'],
                                'description' => $_POST['description'] ?? null,
                                'is_public' => isset($_POST['is_public']) ? (int)$_POST['is_public'] : 0
                            ]
                        );
                        break;
                    case 'deleteProject':
                         $response = $controller->deleteProject(
                            (int)$_POST['project_id'],
                            (int)$_POST['user_id']
                        );
                        break;
                    case 'addProjectMember':
                        $response = $controller->addProjectMember(
                            (int)$_POST['project_id'],
                            (int)$_POST['user_id']
                        );
                        break;
                    case 'getMembers':
                        $response = $controller->getMembers((int)$_POST['project_id']);
                        break;
                    case 'getProjectMembers':
                        $response = $controller->getProjectMembers((int)$_POST['project_id']);
                        break;
                    case 'removeMember':
                        $response = $controller->removeMember(
                            (int)$_POST['project_id'],
                            (int)$_POST['user_id']
                        );
                        break;
                    default:
                        $response = ['success' => false, 'errors' => ['Неверное действие: ' . htmlspecialchars($action)]];
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