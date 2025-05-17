<?php
    class ProjectsContext extends Projects {
        private DBConnect $db;

        public function __construct(DBConnect $db, $params) {
            parent::__construct($params);
            $this->db = $db;
        }

        public function Add() {
            $this->db->QueryExecute(
                "INSERT INTO projects (name, description, is_public) VALUES (?, ?, ?)",
                [
                    $this->name,
                    $this->description,
                    $this->isPublic ? 1 : 0
                ]
            );
        }
    }
?>