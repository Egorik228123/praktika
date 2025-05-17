<?php
    class Projects {
        public string $name;
        public ?string $description;
        public bool $isPublic;

        public function __construct($params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            $this->name = $params->name;
            $this->description = $params->description;
            $this->isPublic = $params->is_public;
        }

        public function Add() {
            
        }
    }
?>