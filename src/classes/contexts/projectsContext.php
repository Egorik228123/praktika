<?php
    require_once __DIR__ . "/../models/Projects.php";
    require_once __DIR__ . "/../DB.php";
    require_once __DIR__ . "/TasksContext.php";

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
                (int)($projectData['is_public'] ?? 0)
            ]);

            $projectId = $this->db->lastInsertId();

            $defaultColumns = ['новые', 'в процессе', 'можно проверять', 'готово'];
            foreach ($defaultColumns as $position => $name) {
                $this->db->QueryExecute(
                    "INSERT INTO columns (name, position, project_id) VALUES (?, ?, ?)",
                    [$name, $position, $projectId]
                );
            }

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
                    $params[] = ($key === 'is_public') ? (int)$value : $value;
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

            $result = $this->db->Query(
                "SELECT id FROM columns WHERE project_id = ?",
                [$projectId]
            );
            $columns = $result->fetch_all(MYSQLI_ASSOC);

            $tasksContext = new TasksContext($this->db);
            foreach ($columns as $column) {
                $tasksContext->deleteTasksByColumn($column['id']);
            }

            $this->db->QueryExecute(
                "DELETE FROM columns WHERE project_id = ?",
                [$projectId]
            );

            $this->db->QueryExecute(
                "DELETE FROM projects WHERE id = ?",
                [$projectId]
            );
            // Уведомление пользователя будет реализовано на стороне клиента (в projects.php) через alert
        }

        public function removeMember(int $projectId, int $userId): void {
            $affectedRows = $this->db->QueryExecute(
                "DELETE FROM project_roles
                WHERE id_project = ? AND id_user = ? AND role != 'creator'",
                [$projectId, $userId]
            );
            if ($affectedRows === 0) {
                throw new Exception("Не удалось удалить участника");
            }
        }

        public function addMember(int $projectId, int $userId, string $role): void {
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

        public function updateMemberRole(int $projectId, int $userId, string $role): void {
            $this->db->QueryExecute(
                "UPDATE project_roles SET role = ? WHERE id_project = ? AND id_user = ? AND role != 'creator'",
                [$role, $projectId, $userId]
            );
        }

        public function getUserRoleInProject(int $userId, int $projectId): ?string {
            $result = $this->db->Query(
                "SELECT role FROM project_roles WHERE id_user = ? AND id_project = ?",
                [$userId, $projectId]
            );
            if ($data = $result->fetch_assoc()) {
                return $data['role'];
            }
            return null;
        }


        public function getAllProjectsForUser(int $userId): array {
            $result = $this->db->Query(
                "SELECT
                    p.id AS project_id,
                    p.name,
                    p.description,
                    p.is_public,
                    
                    MIN(CONCAT(u.surname, ' ', u.name, ' ', COALESCE(u.middlename, ''))) AS creator_name,
                    MIN(pr_creator.id_user) AS creator_id
                FROM
                    projects p
                JOIN
                    project_roles pr ON p.id = pr.id_project
                LEFT JOIN
                    project_roles pr_creator ON p.id = pr_creator.id_project AND pr_creator.role = 'creator'
                LEFT JOIN
                    users u ON pr_creator.id_user = u.id
                WHERE
                    pr.id_user = ? OR p.is_public = 1
                GROUP BY p.id
                ORDER BY p.id DESC",
                [$userId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function getProjectMembers(int $projectId): array {
            $result = $this->db->Query(
                "SELECT u.id, u.name, u.surname, pr.role
                FROM project_roles pr
                JOIN users u ON pr.id_user = u.id
                WHERE pr.id_project = ?
                ORDER BY
                    pr.role = 'creator' DESC, 
                    u.name ASC", // Это для того, чтобы создатель был первым
                [$projectId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        
        public function getProjectCreator(int $projectId): ?array {
            $result = $this->db->Query(
                "SELECT u.id, u.name, u.surname, u.middlename
                FROM project_roles pr
                JOIN users u ON pr.id_user = u.id
                WHERE pr.id_project = ? AND pr.role = 'creator'",
                [$projectId]
            );
            if ($data = $result->fetch_assoc()) {
                return $data;
            }
            return null;
        }
    }
?>