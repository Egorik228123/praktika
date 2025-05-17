<?php
    class projectsContext extends Projects {
        private DBConnect $db;

        public function __construct(DBConnect $db, $params) {
            parent::__construct($params);
            $this->db = $db;
        }

        public function Insert() {
            $this->db->QueryExecute(
                "INSERT INTO projects (name, description, is_public) VALUES (?, ?, ?)",
                [
                    $this->name,
                    $this->description,
                    $this->isPublic ? 1 : 0
                ]
            );
        }

        public function Update() {
            $this->db->QueryExecute(
                "UPDATE projects SET name = ?, description = ?, is_public = ? WHERE id = ?",
                [
                    $this->name,
                    $this->description,
                    $this->isPublic ? 1 : 0,
                    $this->id
                ]
            );
        }

        public function Delete() {
            $this->db->QueryExecute(
                "DELETE FROM `projects` WHERE ?",
                [
                    $this->id
                ]
            );
        }

        public function Select() {
            $this->db->Query(
                "SELECT `id`, `name`, `description`, `is_public` FROM `projects` WHERE ?",
                [
                    $this->id
                ]
            );
        }
    }
?>