<?php
    class Tasks {
        public int $id = 0;
        public string $name;
        public ?string $description;
        public ?string $due_date;
        public int $column_id;

        public function __construct($params) {
            if(is_array($params)) {
                $params = (object)$params;
            }
            $this->id = $params->id ?? 0;
            $this->name = $params->name;
            $this->description = $params->description ?? null;
            $this->due_date = $params->due_date ?? null;
            $this->column_id = $params->column_id;
        }
    }
?>