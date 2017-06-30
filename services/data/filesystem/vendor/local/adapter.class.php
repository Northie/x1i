<?php

namespace services\data\filesystem\vendor\local;

class adapter extends \services\data\adapter {

	
	public function __construct( $settings = '') {
		$this->path = $settings['path'];
		if($settings['namespace']) {
			$namespace = \utils\Tools::filePathProtect($namespace);
			$this->path.='../namespaces/'.$namespace;
		}
	}

	public function create($key, $data) {
		if($this->read($key)) {
			throw new \Exception("Could not create ".__NAMESPACE__.": Key exists");
		}
		
		$path = $this->path.$key;
		
		return ($this->write($path, $data) ? $key : false);
	}

	public function read($key) {
		
		$path = \utils\Tools::filePathProtect($this->path.$key);

		if($this->exists($key)) {
			$data = file_get_contents($path);
			return $data;
		} else {
			return false;
		}
		
	}


	public function update($key, $data) {

		if ($this->exists($key)) {
			
			$path = \utils\Tools::filePathProtect($this->path.$key);
			
			return ($this->write($path, $data) ? $key : false);
		} else {
			throw new \Exception("Could not update ".__NAMESPACE__.": Key does not exist");
		}
		
		
		
	}

	public function delete($key) {
		$exists = 0;

		if ($this->exists($key)) {
			$path = $this->path.$key;
			return unlink($path);
		} else {
			throw new \Exception("Could not delete ".__NAMESPACE__.": Key does not exist");
		}
	}
	
	public function exists($key) {
		$path = \utils\Tools::filePathProtect($this->path.$key);
		return file_exists($path);
	}
	
	private function write($path, $data) {
		return file_put_contents($path, $data);
	}

}
