<?php

namespace OS;

class App {
	use \utils\traits\singleton;
	use \Plugins\helper;
	
	private $modules = [];
	
	private $appTimeLimit = 30;
	
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
		
		list($type,$vendor) = explode("/",$cacheSettings['type']);
		
		$factoryString = "\\services\\data\\$type\\vendor\\$vendor\\factory";
		
		$cacheAdapter = $factoryString::Build($cacheSettings);
		
		\settings\registry::Load()->set('APP_CACHE',$cacheAdapter);
	}
	
	private function initPlugins() {
		\Plugins\EventManager::RegisterHandlers();
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
	
	public function getModules() {
		return $this->modules;
	}
	
	public function setTimeLimit($t) {
		if(!$this->before('SetTimeLimit',$this,['t'=>$t])) {
			return false;
		}
		$tg = (int) $t;
		$this->appTimeLimit = ini_get('max_execution_time');
		set_time_limit($t);
		$this->after('SetTimeLimit',$this,['t'=>$t]);
		
		
	}
	
	public function resetTimeLimit() {
		set_time_limit($this->appTimeLimit);
	}
			
}

