<?php
    require_once __DIR__ . "/../contexts/ProjectsContext.php";
    // Убедитесь, что DB.php включен, если он еще не включен ProjectsContext.php
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

        // Создание проекта
        public function createProject(array $projectData): array {
            try {
                if (empty($projectData['name'])) {
                    $this->addError("Название проекта обязательно");
                    return ['success' => false, 'errors' => $this->errors];
                }
                // Убедитесь, что is_public правильно приведено к int из POST-данных
                $projectData['is_public'] = isset($projectData['is_public']) ? (int)$projectData['is_public'] : 0;

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
                // Убедитесь, что is_public правильно приведено к int из POST-данных
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

        // Новая функция для получения всех проектов для пользователя
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

        // Упрощенное добавление участника (роль по умолчанию 'user')
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
                    case 'getAllUserProjects': // Новый случай для получения всех проектов, связанных с пользователем
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
                                'is_public' => isset($_POST['is_public']) ? (int)$_POST['is_public'] : 0 // Приведение к int здесь
                            ]
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