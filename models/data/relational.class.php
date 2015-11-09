<?php

namespace models\data;

abstract class relational extends data {

	protected $db;

	public function __construct($label) {
		$this->db = \services\data\relational\factory::Build($label);
		$this->setProvider($this->db);
	}

}
