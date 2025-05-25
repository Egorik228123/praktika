<?php
    require_once __DIR__ . "/../models/Columns.php";
    require_once __DIR__ . "/../DB.php";

    class ColumnsContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        // Создание столбца
        public function createColumn(array $columnData): int {
            $sql = "INSERT INTO columns (name, position, project_id) VALUES (?, ?, ?)";
            return $this->db->QueryExecute($sql, [
                $columnData['name'],
                $columnData['position'],
                $columnData['project_id']
            ]);
        }

        // Обновление столбца
        public function updateColumn(int $columnId, array $fields): void {
            $allowed = ['name', 'position'];
            $updates = [];
            $params = [];
            
            foreach ($fields as $key => $value) {
                if (in_array($key, $allowed)) {
                    $updates[] = "`$key` = ?";
                    $params[] = $value;
                }
            }
            
            if (empty($updates))
                throw new Exception("Нет полей для обновления");
            
            $sql = "UPDATE columns SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $columnId;
            $this->db->QueryExecute($sql, $params);
        }

        // Удаление столбца
        public function deleteColumn(int $columnId): void {
            $this->db->QueryExecute("DELETE FROM columns WHERE id = ?", [$columnId]);
        }

        // Получение столбцов проекта
        public function getColumnsByProject(int $projectId): array {
            $result = $this->db->Query(
                "SELECT * FROM columns WHERE project_id = ? ORDER BY id",
                [$projectId]
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
?>