<?php
    require_once __DIR__ . "/../models/Projects.php";
    require_once __DIR__ . "/../DB.php";

    class ProjectsContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        public function createProject(array $projectData): int {
            $this->db->QueryExecute(
                "INSERT INTO projects (name, description, is_public) VALUES (?, ?, ?)", [
                $projectData['name'],
                $projectData['description'] ?? null,
                $projectData['is_public'] ?? 0
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

        public function deleteProject(int $projectId): void {
            // Удаление ролей проекта
            $this->db->QueryExecute(
                "DELETE FROM project_roles WHERE id_project = ?",
                [$projectId]
            );
            
            // Получение столбцов проекта
            $result = $this->db->Query(
                "SELECT id FROM columns WHERE project_id = ?",
                [$projectId]
            );
            $columns = $result->fetch_all(MYSQLI_ASSOC);
            
            // Удаление задач и связанных данных
            foreach ($columns as $column) {
                $tasksContext = new TasksContext($this->db);
                $tasksContext->deleteTasksByColumn($column['id']);
            }
            
            // Удаление столбцов проекта
            $this->db->QueryExecute(
                "DELETE FROM columns WHERE project_id = ?",
                [$projectId]
            );
            
            // Удаление самого проекта
            $this->db->QueryExecute(
                "DELETE FROM projects WHERE id = ?",
                [$projectId]
            );
        }

        public function removeMember(int $projectId, int $userId): void {
            $this->db->QueryExecute(
                "DELETE FROM project_roles 
                WHERE id_project = ? AND id_user = ?",
                [$projectId, $userId]
            );
        }

        public function addMember(int $projectId, int $userId, string $role): void {
            // Проверка существования связи
            $result = $this->db->Query(
                "SELECT * FROM project_roles 
                WHERE id_project = ? AND id_user = ?",
                [$projectId, $userId]
            );
            
            if ($result->num_rows === 0) {
                $this->db->QueryExecute(
                    "INSERT INTO project_roles (id_project, id_user, role) VALUES (?, ?, ?)",
                    [$projectId, $userId, $role]
                );
            }
        }

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

        public function getPublicProjects(): array {
            $result = $this->db->Query(
                "SELECT 
                    p.id AS project_id,
                    p.name,
                    p.description,
                    p.is_public,
                    CONCAT(u.surname, ' ', u.name, ' ', COALESCE(u.middlename, '')) AS creator_name,
                    u.id AS creator_id
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

        public function getProjectMembers(int $projectId): array {
            $result = $this->db->Query(
                "SELECT u.id, u.name, u.surname 
                FROM project_roles pr
                JOIN users u ON pr.id_user = u.id
                WHERE pr.id_project = ?",
                [$projectId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
?>