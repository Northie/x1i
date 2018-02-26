<?php

namespace services\data;


class factory {

	public static function Build($settings,$type=null) {
            
                if(!$type) {
                    if(isset($settings['type']) && isset($settings['vendor'])) {
                        $type = $settings['type'];
                        $vendor = $settings['vendor'];
                    } else {
                        throw new \Exception('type not supplied in settings or function call');
                    }
                } else {
                    $vendor = $settings['type'];
                }
            
                $clsString = "\\services\\data\\".$type."\\vendor\\".$vendor."\\adapter";
            
                $o = new $clsString($settings);
		return $o;
	}

}


