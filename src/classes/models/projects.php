<?php
    class Projects {
        public int $id = 0;
        public string $name;
        public ?string $description;
        public ?bool $isPublic;

        public function __construct($params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            $this->id = $params->id ?? 0;
            $this->name = $params->name;
            $this->description = $params->description ?? null;
            $this->isPublic = $params->is_public ?? false;
        }
    }
?>