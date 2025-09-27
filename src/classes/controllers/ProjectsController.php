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

        public function updateProject(int $projectId, array $projectData, int $userId): array {
            try {
                $role = $this->projectsContext->getUserRoleInProject($userId, $projectId);
                if ($role !== 'creator') {
                    return ['success' => false, 'errors' => ['Только создатель может редактировать проект.']];
                }
                
                if (isset($projectData['is_public'])) {
                    $projectData['is_public'] = (int)$projectData['is_public'];
                }

                $this->projectsContext->updateProject($projectId, $projectData);
                return ['success' => true];
            } catch (Exception $e) {
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

        public function addProjectMember(int $projectId, int $userId, string $role, int $callingUserId): array {
            try {
                 $callerRole = $this->projectsContext->getUserRoleInProject($callingUserId, $projectId);
                if ($callerRole !== 'creator') {
                    return ['success' => false, 'errors' => ['Только создатель может добавлять участников.']];
                }

                // Валидация роли
                if (!in_array($role, ['admin', 'user'])) {
                    return ['success' => false, 'errors' => ['Некорректная роль.']];
                }

                $this->projectsContext->addMember($projectId, $userId, $role);
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

        public function removeMember(int $projectId, int $userId, int $callingUserId): array {
            try {
                $role = $this->projectsContext->getUserRoleInProject($callingUserId, $projectId);
                if ($role !== 'creator') {
                    return ['success' => false, 'errors' => ['Только создатель может удалять участников.']];
                }
                $this->projectsContext->removeMember($projectId, $userId);
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function updateMemberRole(int $projectId, int $callingUserId, int $targetUserId, string $newRole): array {
            try {
                $callerRole = $this->projectsContext->getUserRoleInProject($callingUserId, $projectId);
                if ($callerRole !== 'creator') {
                    return ['success' => false, 'errors' => ["Только создатель проекта может изменять роли."]];
                }
                if (!in_array($newRole, ['admin', 'user'])) {
                    return ['success' => false, 'errors' => ["Некорректная роль."]];
                }
                if ($callingUserId === $targetUserId) {
                    return ['success' => false, 'errors' => ["Нельзя изменить свою роль."]];
                }
                $this->projectsContext->updateMemberRole($projectId, $targetUserId, $newRole);
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'errors' => [$e->getMessage()]];
            }
        }

        public function getProjectRole(int $userId, int $projectId): array {
            try {
                $role = $this->projectsContext->getUserRoleInProject($userId, $projectId);
                return ['success' => true, 'data' => ['role' => $role]];
            } catch (Exception $e) {
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
                        $_POST['user_id'] = $userId; 
                        $response = $controller->createProject($_POST); 
                        break;
                    case 'updateProject': 
                        $response = $controller->updateProject((int)$_POST['project_id'], $_POST, $userId); break;
                    case 'deleteProject': 
                        $response = $controller->deleteProject((int)$_POST['project_id'], (int)$_POST['user_id']); 
                        break;
                    case 'addProjectMember':
                        $response = $controller->addProjectMember(
                            (int)$_POST['project_id'],
                            (int)$_POST['user_id'],
                            $_POST['role'] ?? 'user',
                            $userId
                        );
                        break;
                    case 'getMembers': 
                        $response = $controller->getMembers((int)$_POST['project_id']); 
                        break;
                    case 'getProjectMembers': 
                        $response = $controller->getProjectMembers((int)$_POST['project_id']); 
                        break;
                    case 'removeMember': 
                        $response = $controller->removeMember((int)$_POST['project_id'], (int)$_POST['user_id'], $userId); 
                        break;
                    case 'updateMemberRole': 
                        $response = $controller->updateMemberRole((int)$_POST['project_id'], $userId, (int)$_POST['user_id'], $_POST['role']); 
                        break;
                    case 'getProjectRole': 
                        $response = $controller->getProjectRole($userId, (int)$_POST['project_id']); 
                        break;
                    default: 
                        $response = ['success' => false, 'errors' => ['Неверное действие: ' . htmlspecialchars($action)]];
                }
            } catch (Exception $e) {
                $response = ['success' => false, 'errors' => [$e->getMessage()]];
            }
            echo json_encode($response);
            exit();
        }
    }
    ProjectsController::handleRequest();
?>