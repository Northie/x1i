<?php

namespace services\data\relational\vendor\mysql;

class factory {

	public static function Build($settings) {
            
            ksort($settings);
            $id = json_encode($settings);
            $a = \settings\registry::Load()->get($id);
            if(!$a) {
                $connector = new \services\data\relational\connector($settings); //relational / pdo uses a common connection mechanism
                //give the vendor specific adapter the pdo connection
                $a = new adapter($connector);
                \settings\registry::Load()->set($id,$a);
            }
            return $a;
	}

}
