<?php

namespace modules;

class factory {
    public static function Build($module) {
        
            $cls = "\\".implode("\\",['modules',$module,'init']);
            $o = new $cls;
            return $o;
    }
}

