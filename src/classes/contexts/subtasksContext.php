<?php
    class SubtasksContext extends Subtasks 
    {
        private DBConnect $db;

        public function __construct(DBConnect $db, $params) {
            parent::__construct($params);
            $this->db = $db;
        }

        public function Insert() 
        {
            $this->db->QueryExecute(
                "INSERT INTO `subtasks` (`name`, `description`, `due_date`, `task_id`) VALUES (?, ?, ?, ?)",
                [
                    $this->name,
                    $this->description,
                    $this->due_date,
                    $this->task_id                
                ]
            );
        }

        public function Update() {
            $this->db->QueryExecute(
                "UPDATE `subtasks` SET `name` = ?, `description` = ?, `due_date` = ?, `task_id` = ? WHERE `id` = ?",
                [
                    $this->name,
                    $this->description,
                    $this->due_date,
                    $this->task_id,
                    $this->id
                ]
            );
        }
        public function Delete() {
            $this->db->QueryExecute(
                "DELETE FROM `subtasks` WHERE `id` = ?",
                [ $this->id ]
            );
        } 
        
        public function Select() {
            $this->db->Query(
                "SELECT * FROM `subtasks` WHERE `id` = ?",
                [ $this->id ]
            );
        }
    }
?>