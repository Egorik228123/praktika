<?php
    class Users {
        public int $id;
        public string $name;
        public string $surname;
        public string $middlename;
        public string $email;
        public string $password;
        public ?string $bio;

        public function __construct($params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            $this->name = $params->name;
            $this->surname = $params->surname;
            $this->middlename = $params->middlename;
            $this->email = $params->email;
            $this->password = $params->password;
            $this->bio = $params->bio;
        }
    }
?>