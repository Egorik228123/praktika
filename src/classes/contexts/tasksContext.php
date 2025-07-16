<?php
    require_once __DIR__ . "/../models/tasks.php";
    require_once __DIR__ . "/../DB.php";

    class TasksContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        public function updateTask(int $taskId, array $fields): void {
            $allowed = ['name', 'description', 'due_date', 'column_id'];
            $updates = [];
            $params = [];
            
            foreach ($fields as $key => $value) {
                if (in_array($key, $allowed)) {
                    $updates[] = "`$key` = ?";
                    $params[] = $value;
                }
            }
            
            if (empty($updates)) {
                // Если нет полей для обновления, просто выходим
                return;
            }
            
            $sql = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $taskId;
            $this->db->QueryExecute($sql, $params);
        }

        public function deleteTask(int $taskId): void {
            // Удаление связанных подзадач
            $this->db->QueryExecute("DELETE FROM subtasks WHERE task_id = ?", [$taskId]);
            // Удаление связи с ответственными
            $this->db->QueryExecute("DELETE FROM task_assignees WHERE task_id = ?", [$taskId]);
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
            // Проверяем, не назначен ли уже пользователь
            $result = $this->db->Query(
                "SELECT * FROM task_assignees WHERE task_id = ? AND user_id = ?",
                [$taskId, $userId]
            );
            
            if ($result->num_rows === 0) {
                $this->db->QueryExecute(
                    "INSERT INTO task_assignees (task_id, user_id) VALUES (?, ?)",
                    [$taskId, $userId]
                );
            }
        }

        public function getTasksByColumn(int $columnId): array {
            $result = $this->db->Query(
                "SELECT 
                    tasks.id AS id,
                    tasks.name AS name,
                    COALESCE(GROUP_CONCAT(CONCAT(users.name, ' ', users.surname) SEPARATOR ', '), 'Не назначен') AS assignee
                FROM 
                    tasks
                LEFT JOIN 
                    task_assignees ON tasks.id = task_assignees.task_id
                LEFT JOIN 
                    users ON task_assignees.user_id = users.id
                WHERE 
                    tasks.column_id = ?
                GROUP BY
                    tasks.id, tasks.name
                ORDER BY
                    tasks.id;
                ", // Добавлен GROUP BY и ORDER BY для корректного отображения
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

        public function getTaskWithDetails(int $taskId): array {
            $result = $this->db->Query(
                "SELECT * FROM tasks WHERE id = ?",
                [$taskId]
            );
            return $result->fetch_assoc() ?: []; // Возвращаем пустой массив, если задача не найдена
        }

        public function getTaskAssignees(int $taskId): array {
            $result = $this->db->Query(
                "SELECT u.id, u.name, u.surname 
                FROM task_assignees ta
                JOIN users u ON ta.user_id = u.id
                WHERE ta.task_id = ?",
                [$taskId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function getTaskSubtasks(int $taskId): array {
            $result = $this->db->Query(
                "SELECT s.id, s.name, s.description
                FROM subtasks s
                WHERE s.task_id = ?",
                [$taskId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function createTask(array $taskData): int {
            $this->db->QueryExecute(
                "INSERT INTO tasks (name, description, due_date, column_id) 
                VALUES (?, ?, ?, ?)",
                [
                    $taskData['name'],
                    $taskData['description'],
                    $taskData['due_date'],
                    $taskData['column_id']
                ]
            );
            return $this->db->lastInsertId();
        }

        // Добавление ответственного
        public function addAssignee(int $taskId, int $userId): void {
            // Проверяем, не назначен ли уже пользователь
            $result = $this->db->Query(
                "SELECT * FROM task_assignees WHERE task_id = ? AND user_id = ?",
                [$taskId, $userId]
            );
            
            if ($result->num_rows === 0) {
                $this->db->QueryExecute(
                    "INSERT INTO task_assignees (task_id, user_id) VALUES (?, ?)",
                    [$taskId, $userId]
                );
            }
        }

        // Удаление ответственного
        public function removeAssignee(int $taskId, int $userId): void {
            $this->db->QueryExecute(
                "DELETE FROM task_assignees WHERE task_id = ? AND user_id = ?",
                [$taskId, $userId]
            );
        }

        // Создание подзадачи
        public function createSubtask(array $subtaskData): int {
            $this->db->QueryExecute(
                "INSERT INTO subtasks (name, description, task_id) VALUES (?, ?, ?)",
                [
                    $subtaskData['name'],
                    $subtaskData['description'] ?? null,
                    $subtaskData['task_id']
                ]
            );
            return $this->db->lastInsertId();
        }

        // Удаление подзадачи
        public function deleteSubtask(int $subtaskId): void {
            $this->db->QueryExecute(
                "DELETE FROM subtasks WHERE id = ?",
                [$subtaskId]
            );
        }

        // Удаление всех задач в столбце (не используется в текущей логике, но оставлено для полноты)
        public function deleteTasksByColumn(int $columnId): void {
            $result = $this->db->Query(
                "SELECT id FROM tasks WHERE column_id = ?",
                [$columnId]
            );
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach ($tasks as $task) {
                $this->deleteTask($task['id']);
            }
        }

        // Перемещение всех задач между столбцами
        public function moveAllTasks(int $fromColumnId, int $toColumnId): void {
            $this->db->QueryExecute(
                "UPDATE tasks SET column_id = ? WHERE column_id = ?",
                [$toColumnId, $fromColumnId]
            );
        }

        public function updateAssignees(int $taskId, array $assigneeIds): void {
            // Удаляем всех текущих ответственных для этой задачи
            $this->db->QueryExecute("DELETE FROM task_assignees WHERE task_id = ?", [$taskId]);
            
            // Добавляем новых ответственных
            foreach ($assigneeIds as $userId) {
                // Убедимся, что userId является целым числом, чтобы избежать ошибок SQL
                $userId = (int) $userId;
                if ($userId > 0) {
                    $this->addAssignee($taskId, $userId);
                }
            }
        }

        public function updateSubtasks(int $taskId, array $subtasks): void {
            // Получаем текущие подзадачи для данной задачи
            $currentSubtasksResult = $this->db->Query(
                "SELECT id FROM subtasks WHERE task_id = ?",
                [$taskId]
            );
            $currentSubtaskIds = array_column($currentSubtasksResult->fetch_all(MYSQLI_ASSOC), 'id');
            
            $updatedSubtaskIds = [];

            foreach ($subtasks as $subtask) {
                if (isset($subtask['id']) && $subtask['id'] > 0) {
                    // Обновление существующей подзадачи
                    $this->db->QueryExecute(
                        "UPDATE subtasks SET name = ?, description = ? WHERE id = ?",
                        [$subtask['name'], $subtask['description'], $subtask['id']]
                    );
                    $updatedSubtaskIds[] = $subtask['id'];
                } else {
                    // Создание новой подзадачи
                    $this->db->QueryExecute(
                        "INSERT INTO subtasks (task_id, name, description) VALUES (?, ?, ?)",
                        [$taskId, $subtask['name'], $subtask['description']]
                    );
                    $updatedSubtaskIds[] = $this->db->lastInsertId();
                }
            }

            // Удаляем подзадачи, которые были удалены из списка на клиенте
            $subtasksToDelete = array_diff($currentSubtaskIds, $updatedSubtaskIds);
            foreach ($subtasksToDelete as $subtaskId) {
                $this->deleteSubtask($subtaskId);
            }
        }
    }
?>