<?php

namespace OS;

class App {
    use \utils\traits\singleton;
    use \Plugins\helper;
    
    private $modules = [];
    
    private function __construct() {

    }
    
    public function start() {
        $r = new \ReflectionObject($this);
        foreach ($r->getMethods() as $method) {
            if(strpos($method->getName(),"init") === 0) {
                $this->{$method->getName()}();
            }
        }
        $this->after(__METHOD__, $this);
        
    }
    
    private function initCache() {
        
        $cacheSettings = \settings\database::Load()->get('app_cache');
        
        $adapterString = "\\services\\data\\cache\\vendor\\".$cacheSettings['type']."\\factory";
        
        $cacheAdapter = $adapterString::Build($cacheSettings);
        
        \settings\registry::Load()->set('APP_CACHE',$cacheAdapter);
    }
    
    private function initPlugins() {
        \Plugins\Plugins::RegisterPlugins();
    }
    
    public function addModule($module) {
        
        list($a,$b,$c) = explode("\\",get_class($module));
        
        $this->modules[$b] = $module;
    }
    
    
    public function getModule($moduleName) {
        return isset($this->modules[$moduleName]) ? $this->modules[$moduleName] : null;
    }
    
    public function preloadModules($modules) {
        foreach ($modules as $module) {
            //\modules\factory::Build($module);
        }
    }
}

