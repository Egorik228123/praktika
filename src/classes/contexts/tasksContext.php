<?php
    class TasksContext extends Tasks {
        private DBConnect $db;

        public function __construct(DBConnect $db, $params) {
            parent::__construct($params);
            $this->db = $db;
        }

        public function Insert() {
            $this->db->QueryExecute(
                "INSERT INTO tasks (name, description, due_date, column_id) VALUES (?, ?, ?, ?)",
                [
                    $this->name,
                    $this->description,
                    $this->due_date,
                    $this->column_id                
                ]
            );
        }
        
        public function Update() {
            $this->db->QueryExecute(
                "UPDATE tasks SET name = ?, description = ?, due_date = ?, column_id = ? WHERE id = ?",
                [
                    $this->name,
                    $this->description,
                    $this->due_date,
                    $this->column_id,
                    $this->id
                ]
            );
        }

        public function Delete() {
            $this->db->QueryExecute(
                "DELETE FROM tasks WHERE id = ?",
                [ $this->id ]
            );
        } 

        public function Select() {
            $this->db->Query(
                "SELECT * FROM tasks WHERE id = ?",
                [ $this->id ]
            );
        }
    }
?>