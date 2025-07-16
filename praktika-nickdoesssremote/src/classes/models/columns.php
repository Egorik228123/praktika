<?php
    class Columns {
        public int $id = 0;
        public string $name;
        public int $position;
        public int $projectId;

        public function __construct($params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            $this->id = $params->id ?? 0;
            $this->name = $params->name;
            $this->position = $params->position ?? 0;
            $this->projectId = $params->projectId;
        }
    }
?>