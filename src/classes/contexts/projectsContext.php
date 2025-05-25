<?php
    require_once __DIR__ . "/../models/Projects.php";
    require_once __DIR__ . "/../DB.php";

    class ProjectsContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        // Создание проекта и столбцов
        public function createProject(array $projectData): int {
            $this->db->QueryExecute(
                "INSERT INTO projects (name, description, is_public) VALUES (?, ?, ?)", [
                $projectData['name'],
                $projectData['description'] ?? null,
                $projectData['is_public'] ? 1 : 0
            ]);
            
            $projectId = $this->db->lastInsertId();
            
            // Создание стандартных столбцов
            $defaultColumns = ['новые', 'в процессе', 'можно проверять', 'готово'];
            foreach ($defaultColumns as $position => $name) {
                $this->db->QueryExecute(
                    "INSERT INTO columns (name, position, project_id) VALUES (?, ?, ?)",
                    [$name, $position, $projectId]
                );
            }

            // Создание роли
            $this->db->QueryExecute(
                "INSERT INTO project_roles (id_project, id_user, role) VALUES (?, ?, ?)",
                [$projectId, $projectData['user_id'], 'creator']
            );
            
            return $projectId;
        }

        // Обновление данных проекта
        public function updateProject(int $projectId, array $fields): void {
            $allowed = ['name', 'description', 'is_public'];
            $updates = [];
            $params = [];
            
            foreach ($fields as $key => $value) {
                if (in_array($key, $allowed)) {
                    $updates[] = "`$key` = ?";
                    $params[] = ($key === 'is_public') ? ($value ? 1 : 0) : $value;
                }
            }
            
            if (empty($updates)) throw new Exception("Нет полей для обновления");
            
            $sql = "UPDATE projects SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $projectId;
            $this->db->QueryExecute($sql, $params);
        }

        // Удаление проекта
        public function deleteProject(int $projectId): void {
            $this->db->QueryExecute("DELETE FROM projects WHERE id = ?", [$projectId]);
        }

        // Добавление участника в проект
        public function addMember(int $projectId, int $userId, string $role): void {
            $this->db->QueryExecute(
                "INSERT INTO project_roles (id_project, id_user, role) VALUES (?, ?, ?)",
                [$projectId, $userId, $role]
            );
        }

        // Получение списка участников проекта
        public function getMembers(int $projectId): array {
            $result = $this->db->Query(
                "SELECT u.*, pr.role 
                FROM project_roles pr 
                JOIN users u ON pr.id_user = u.id 
                WHERE pr.id_project = ?",
                [$projectId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        // Получение проекта по ID
        public function getProjectById(int $projectId): ?Projects {
            $result = $this->db->Query(
                "SELECT * FROM projects WHERE id = ?",
                [$projectId]
            );
            if ($data = $result->fetch_object()) {
                return new Projects($data);
            }
            return null;
        }

        // Получение проектов
        public function getPublicProjects(): array {
            $result = $this->db->Query(
                "SELECT 
                    p.name AS 'name',
                    CONCAT(u.surname, ' ', u.name, ' ', u.middlename) AS 'creator_name',
                    u.id AS 'creator_id',
                    p.id AS 'project_id'
                FROM 
                    projects p
                JOIN 
                    project_roles pr ON p.id = pr.id_project
                JOIN 
                    users u ON pr.id_user = u.id
                WHERE 
                    p.is_public = 1
                    AND pr.role = 'creator'"
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
?>