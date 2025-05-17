<?php
    class columnsContext extends Columns {
        private DBConnect $db;

        public function __construct(DBConnect $db, $params) {
            parent::__construct($params);
            $this->db = $db;
        }

        public function Insert() {
            $this->db->QueryExecute(
                "INSERT INTO columns (name, position, projectId) VALUES (?, ?, ?)",
                [
                    $this->name,
                    $this->position,
                    $this->projectId
                ]
            );
        }

        public function Update() {
            $this->db->QueryExecute(
                "UPDATE columns SET `name`= ?, `position`= ? WHERE id = ?",
                [
                    $this->name,
                    $this->position,
                    $this->id
                ]
            );
        }

        public function Delete() {
            $this->db->QueryExecute(
                "DELETE FROM `columns` WHERE id = ?",
                [
                    $this->id
                ]
            );
        }

        public function Select() {
            $this->db->Query(
                "SELECT `name`, `position`, `project_id` FROM `columns` WHERE id = ?",
                [
                    $this->id
                ]
            );
        }
    }
?>