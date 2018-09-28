<?php

namespace Plugins;

class EventManager {

	private static $instance;
	public $handlers = array();
	static $use_plugins = false;
	public $events = [];
	public $triggered = [];

	private function __construct() {
		
	}

	public static function RegisterHandlers() {

		$plugin_paths = [realpath(\X1_APP_PATH . "/plugins") => true];

		$modulePath = realpath(\X1_APP_PATH . "/modules");

		$modules = scandir($modulePath);
		foreach ($modules as $module) {
			$path = realpath($modulePath . "/" . $module . "/plugins");
			if (file_exists($path)) {
				$plugin_paths[$path] = true;
			}
		}

		foreach ($plugin_paths as $plugin_path => $yes) {

			$files = scandir($plugin_path);  //cache? auto generate? like with class list?

			for ($i = 0; $i < count($files); $i++) {
				if (strpos($files[$i], ".plugin.class.php") > -1) {
					//require_once($plugin_path .'/'. $files[$i]);
					$plugins[] = str_replace(".plugin.class.php", "", $files[$i]);
				}
			}
		}

		for ($i = 0; $i < count($plugins); $i++) {
			if (method_exists('\Plugins\\' . $plugins[$i], "RegisterMe")) {
				call_user_func(array('\Plugins\\' . $plugins[$i], "RegisterMe"));
			}
		}

		self::$use_plugins = true;
	}

	public static function Load() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public function RegisterHandler($handler, $event) {

		$this->handlers[$event][] = "\\" . $handler;
	}

	public function ObserveEvent($event, $obj, $options = false) {

		$this->events[$event] ++;

		if (!self::$use_plugins) {
			return true;
		}

		for ($i = 0; $i < count($this->handlers[$event]); $i++) {
			$tmp = new $this->handlers[$event][$i];
			$this->triggered[$when][] = $this->handlers[$event][$i];
			set_time_limit(30); //update with settings default???
			$o = $tmp->Initiate($obj, $options, $event);
			set_time_limit(30); //update with settings default???
			if (!$o) {
				return false;
			}
		}

		return true;
	}

	public function debug() {
		return [
			'registeredHandlers' => $this->handlers,
			'observedEvents' => $this->events,
			'tiggeredHandlers' => $this->triggered
		];
	}

}
