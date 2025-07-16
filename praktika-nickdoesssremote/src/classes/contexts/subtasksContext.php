<?php
    require_once __DIR__ . "/../models/Subtasks.php";
    require_once __DIR__ . "/../DB.php";

    class SubtasksContext {
        private DBConnect $db;

        public function __construct(DBConnect $db) {
            $this->db = $db;
        }

        
    }
?>