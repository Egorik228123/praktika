<?php
    class DBConnect {
        private mysqli $db;

        public function __construct() {
            $this->db = new mysqli(
                'localhost', 
                'root', 
                '', 
                'praktika_oshepkov'
            );
        }

        public function Query(string $query, array $params = []) {
            $query = $this->db->prepare($query);

            if(!empty($params)) {
                $types = $this->paramTypes($params);
                $query->bind_param($types, ...$params);
            }
            $query->execute();
            $result = $query->get_result();

            return $result;
        }

        public function QueryExecute(string $query, array $params = []) {
            $query = $this->db->prepare($query);

            if(!empty($params)) {
                $types = $this->paramTypes($params);
                $query->bind_param($types, ...$params);
            }
            $query->execute();
            $result = $query->affected_rows;

            return $result;
        }

        public function paramTypes($params) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                }
                elseif (is_float($param)) {
                    $types .= 'd';
                }
                else {
                    $types .= 's';
                }
            }
            return $types;
        }

        public function __destruct() {
            $this->db->close();
        }
    }
?>