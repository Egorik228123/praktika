<?php
    class Columns {
        public string $name;
        public int $position;
        public int $projectId;

        public function __construct($name, $position, $projectId) {
            $this->name = $name;
            $this->position = $position;
            $this->projectId = $projectId;
        }
    }
?>