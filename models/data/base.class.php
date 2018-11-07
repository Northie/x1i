<?php

namespace models\data;

class base extends \models\data\model {
    public $structure = [
        'id' => [false, 'string',['\\models\\data\\base','generateId']],
		'type' => [false, 'string',['\\models\\data\\base','getType']]
    ];
	
	public static function generateId() {
		return \utils\Tools::UUID();
	}
	
	public static function getType() {
		return parent::$type;
	}
}