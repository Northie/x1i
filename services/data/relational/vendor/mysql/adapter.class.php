<?php

namespace services\data\relational\vendor\mysql;

class adapter extends \services\data\adapter {

	public function __construct($db) {
		$this->db = $db;
	}
	
	public function setModelName($model) {
		$this->model = $model;
	}

	public function create($args) {
            
            $values = [];
            foreach($args as $key => $val) {
                $values[] = "$key = :$key";
            }
            
            $fields = '`'.implode("`, `",array_keys($args)).'`';
            
            $sql = "
                INSERT INTO
                    `$this->model` (
                        ".$fields."
                    ) VALUES (
                        ".implode(", ",$values)."
                    )
                ;
            ";

            $this->query($sql,$args);
	}

	public function read($args) {
            
            $where = $this->toWhere($args);
            
            $sql = "
                SELECT
                    *
                FROM
                    `".$this->model."`
                WHERE
                    ".implode(" AND ",$where['sql'])."
            ";
            
            return $this->query($sql, $where['args'])->returnArray();
	}

	public function update() {

	}

	public function delete() {

	}

	public function query($sql, $args) {
		return $this->db->Execute($sql, $args);
	}

	public function getAdapter() {
		return $this->db;
	}
        
        private function toWhere($args) {
            $sql = $binds = [];
            
            foreach($args as $key => $val) {
                $sql[] = "`$key` = :$key";
                $binds[$key] = $val;
            }
            
            return['sql'=>$sql,'args'=>$binds];
        }

}
