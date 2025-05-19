<?php
    class UsersContext extends Users {
        private DBConnect $db;

        public function __construct(DBConnect $db, $params) {
            parent::__construct($params);
            $this->db = $db;
        }

        public function Insert() {
            $this->db->QueryExecute(
                "INSERT INTO `users` (`name`, `surname`, `middlename`, `email`, `password`, `bio`) VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $this->name,
                    $this->surname,
                    $this->middlename,
                    $this->email,
                    $this->password,
                    $this->bio,
                ]
            );
        }

        public function Update() {
            $this->db->QueryExecute(
                "UPDATE `users`
                 SET `name` = ?, `surname` = ?, `middlename` = ?, `email` = ?, `password` = ?, `bio` = ?
                 WHERE `id` = ?",
                [
                    $this->name,
                    $this->surname,
                    $this->middlename,
                    $this->email,
                    $this->password,
                    $this->bio,
                    $this->id,
                ]
            );
        }

        public function Delete() {
            $this->db->QueryExecute(
                "DELETE FROM `users` WHERE `id` = ?",
                [ $this->id ]
            );
        }

        public function Select() {
            $this->db->Query(
                "SELECT `name`, `surname`, `middlename`, `email`, `bio` FROM `users` WHERE `id` = ?",
                [ $this->id ]
            );
        }
    }
?>