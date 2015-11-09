<?php

namespace endpoints;

class factory {
    public static function Build($context,$class,$request,$response,$filters) {
        
        $str = "\\endpoints\\".$context."\\".$class;
        return new $str($request,$response,$filters);
    }
}