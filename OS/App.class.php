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
        $cacheAdapter = \services\data\cache\vendor\couchbase\factory::Build();
        \settings\registry::Load()->set('CACHE',$cacheAdapter);
    }
}

