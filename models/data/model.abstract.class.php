<?php
namespace models\data;

abstract class model {

    public $id = '';
    public static $type = '';
    public $structure = [];
	public $idParam = 'id';

    public function __construct() {

        $r = new \ReflectionObject($this);
        self::$type = trim(str_replace($r->getNamespaceName(), "", $r->getName()), "\\/");
		
		$this->getExtensions($r);
    }
    
    public final function AddSubType($model,$required,$multiple,$fields=[]) {
        	
		$modelString = "\\".__NAMESPACE__."\\".$model;
		
        $oModel = new $modelString;
        
        $structure = $oModel->getStructure();
        
        if($fields) {
            if(!is_array($fields)) {
                $fields = explode(",", $fields);
            }
            $incStructure = [];
            foreach($fields as $field) {
                $incStructure[$field] = $structure[$field];
            }
            $structure = $incStructure;
        }
        
        $this->structure[$model] = [
                'required' => $required,
                'multiple' => $multiple,
                'structure' => $structure
        ];
    }

    public function getStructure() {
        return $this->structure;
    }

    public function setStructure($structure) {
        $this->structure = $structure;
    }
	
	public final function getExtensions($r) {
		
		$hierarchy = [\get_called_class()];
		
		while($parent = $r->getParentClass()) {
			$hierarchy[] = $parent->getName();
			$r = $parent;
		}

		foreach($hierarchy as $level) {
			$extensions = \models\extensionManager::Load($level)->getExtensions();
			if($extensions) {
				foreach($extensions as $extension) {
					$this->setStructure(array_merge(
						$this->getStructure(), $extension
					));		
				}
			}
		}
	}
	
	public function toForm() {
		
		$defintion = [];
		
		foreach($this->getStructure() as $key => $val) {
			$defintion[] = [
				"label"=> \utils\Tools::camel_to_title($key),
				"name"=>$key,
				"required"=>$val[0],
				"input_type"=>$val[1],
				"data_type"=>$val[1]
			];
		}
		return new class(get_called_class(),$defintion) extends \libs\forms\manager {};
	}

}
