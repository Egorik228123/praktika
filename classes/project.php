<?php
    class Project {
        public string $name;
        public ?string $description;
        public bool $isPublic;

        public function __construct($name, $description, $isPublic) {
            $this->name = $name;
            $this->description = $description;
            $this->isPublic = $isPublic;
        }

        public function Add() {
            
        }
    }
?>