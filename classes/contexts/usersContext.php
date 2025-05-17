<?php
    class Users {
        public string $name;
        public string $surname;
        public string $middlename;
        public string $email;
        public string $password;
        public ?string $bio;

        public function __construct($name, $surname, $middlename, $email, $password, $bio) {
            $this->name = $name;
            $this->surname = $surname;
            $this->middlename = $middlename;
            $this->email = $email;
            $this->password = $password;
            $this->bio = $bio;
        }
        public function Add() {
            
        }
    }
?>