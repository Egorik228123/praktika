<?php
    require_once __DIR__ . "/../models/Tasks.php";
    require_once __DIR__ . "/../DB.php";

    class TasksContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        public function createTask(array $taskData): int {
            $sql = "INSERT INTO tasks (name, description, due_date, column_id) 
                    VALUES (?, ?, ?, ?)";
            return $this->db->QueryExecute($sql, [
                $taskData['name'],
                $taskData['description'] ?? null,
                $taskData['due_date'] ?? null,
                $taskData['column_id']
            ]);
        }

        public function updateTask(int $taskId, array $fields): void {
            $allowed = ['name', 'description', 'due_date'];
            $updates = [];
            $params = [];
            
            foreach ($fields as $key => $value) {
                if (in_array($key, $allowed)) {
                    $updates[] = "`$key` = ?";
                    $params[] = $value;
                }
            }
            
            if (empty($updates)) throw new Exception("Нет полей для обновления");
            
            $sql = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $taskId;
            $this->db->QueryExecute($sql, $params);
        }

        public function deleteTask(int $taskId): void {
            // Удаление связанных подзадач (под вопросом ведь есть каскадное удаление)
            $this->db->QueryExecute("DELETE FROM subtasks WHERE task_id = ?", [$taskId]);
            // Удаление задачи
            $this->db->QueryExecute("DELETE FROM tasks WHERE id = ?", [$taskId]);
        }

        public function moveTask(int $taskId, int $newColumnId): void {
            $this->db->QueryExecute(
                "UPDATE tasks SET column_id = ? WHERE id = ?",
                [$newColumnId, $taskId]
            );
        }

        public function assignUser(int $taskId, int $userId): void {
            $this->db->QueryExecute(
                "INSERT INTO task_assignees (user_id, task_id) VALUES (?, ?)",
                [$userId, $taskId]
            );
        }

        public function getTasksByColumn(int $columnId): array {
            $result = $this->db->Query(
                "SELECT 
                    tasks.id AS id,
                    tasks.name AS name,
                    CONCAT(users.name, ' ', users.surname) AS assignee
                FROM 
                    tasks
                LEFT JOIN (
                    SELECT 
                        task_id,
                        MIN(user_id) AS first_user_id
                    FROM 
                        task_assignees
                    GROUP BY 
                        task_id
                ) AS first_assignee ON tasks.id = first_assignee.task_id
                LEFT JOIN 
                    users ON first_assignee.first_user_id = users.id
                WHERE 
                    tasks.column_id = ?;",
                [$columnId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function getTaskById(int $taskId): ?Tasks {
            $result = $this->db->Query(
                "SELECT * FROM tasks WHERE id = ?",
                [$taskId]
            );
            return $result->fetch_object(Tasks::class) ?: null;
        }
    }
?>