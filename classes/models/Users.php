<?php
    class Users {
        public int $id = 0;
        public string $name;
        public string $surname;
        public ?string $middlename = null;
        public string $email;
        public string $password;
        public ?string $bio = null;

        public function __construct($params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            
            $this->id = $params->id ?? 0;
            $this->name = $params->name;
            $this->surname = $params->surname;
            $this->middlename = $params->middlename ?? null;
            $this->email = $params->email;
            $this->password = $params->password;
            $this->bio = $params->bio ?? null;
        }
    }
?>