<?php

namespace views;

trait view {

	protected $data;
	protected $path = false;

	public function __construct() {
		;
	}

	public function setPath($path) {
		$this->path = trim($path, '/');
	}

	public function setData($data) {
		$this->data = $data;
	}
	
	public function getData() {
		return $this->data;
	}

	public function serve() {
		include 'templates/www/index.php';
	}

	public function __get($name) {
		return $this->data[$name];
	}

	public function setFrontEndPath($path) {
		$this->frontEndPath = $path;
	}

	public function includeFile($file,$data) {
		include($file);
	}

	private function renderPartial($partial, $data = []) {
		$fc = \settings\registry::Load()->get('FrontController');

		$req = $fc->request->getNormalisedRequest();

		$default = false;

		if ($partial[0] == '^') {
			$default = true;
			$partial = trim($partial, '^');
		}

		if ($req['module'] && !$default) {
			$path = implode(\DIRECTORY_SEPARATOR, [X1_APP_PATH, 'modules', $req['module'], 'contexts', $req['context'], 'templates']);
		} else {
			$path = implode(\DIRECTORY_SEPARATOR, [X1_APP_PATH, 'contexts', $req['context'], 'templates']);
		}

		$eventOptions = [
			'partial' => $partial,
			'data' => $data,
			'default' => $default,
			'path' => $path
		];

		$file = $path . \DIRECTORY_SEPARATOR . $partial . ".phtml";

		\Plugins\EventManager::Load()->ObserveEvent('oneBeforeRenderPartialInclude', $this, $eventOptions);

		$this->includeFile($file,$data);

		\Plugins\EventManager::Load()->ObserveEvent('oneAfterRenderPartialInclude', $this, $eventOptions);
	}

}
