<?php
    class Columns {
        public int $id;
        public string $name;
        public int $position;
        public int $projectId;

        public function __construct($params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            $this->name = $params->name;
            $this->position = $params->position;
            $this->projectId = $params->projectId;
        }
    }
?>