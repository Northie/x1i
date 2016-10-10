<?php

namespace OS;

class App {
    public function __construct() {

    }
    
    public function start() {
        $r = new \ReflectionObject($this);
        foreach ($r->getMethods() as $method) {
            if(strpos($method->getName(),"init") === 0) {
                $this->{$method->getName()}();
            }
        }
    }
    
    private function initCache() {
        
        $cacheSettings = \settings\database::Load()->get('app_cache');
        
        $adapterString = "\\services\\data\\cache\\vendor\\".$cacheSettings['type']."\\factory";
        
        $cacheAdapter = $adapterString::Build($cacheSettings);
        
        \settings\registry::Load()->set('APP_CACHE',$cacheAdapter);
    }
}

