<?php

namespace services\data\cache;

class factory {

	public static function Build($type = false) {

                if(!$type) {
                    $type = 'apc'; //TODO get default;
                }
            
                $cls = "\\services\\data\\cache\\vendor\\" . $type . "\\adapter";

		$o = new $cls;

		return $o;
	}

}
