<?php

namespace models\data;

class base extends \models\data\model {
    public $structure = [
        'id' => [true, 'string',['\\models\\data\\base','generateId']],
		'type' => [true, 'string',['\\models\\data\\base','getType']]
    ];
	
	public static function generateId() {
		return \utils\Tools::UUID();
	}
	
	public static function getType() {
		return parent::$type;
	}
}