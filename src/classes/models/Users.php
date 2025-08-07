<?php
    class Users {
        public int $id = 0;
        public string $name;
        public string $surname;
        public string $middlename;
        public string $email;
        private ?string $password = null;
        public ?string $bio = null;

        public function __construct(object|array $params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            
            $this->id = $params->id ?? 0;
            $this->name = $params->name ?? '';
            $this->surname = $params->surname ?? '';
            $this->middlename = $params->middlename ?? '';
            $this->email = $params->email ?? '';
            $this->password = $params->password ?? null;
            $this->bio = $params->bio ?? null;
        }

        public function setPassword(string $password): void {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }

        public function getPassword(): ?string {
            return $this->password;
        }
    }
?>