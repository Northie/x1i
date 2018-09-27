<?php

namespace services\data\cache\vendor\none;

class adapter extends \services\data\adapter {

	public function __construct($settings) {
		
	}

	public function create($data, $id = false) {
		return true;
	}

	public function read($key) {
		return false;
	}

	public function update($data, $conditions = false) {
		return $this->create($key, $data);
	}

	public function delete($key, $force = false) {
		return true;
	}

	private function getLifetime() {
		if (($cacheLifetime = \settings\general::Load()->get(['CACHE_LIFETIME']))) {
			return $cacheLifetime;
		}
		return 3600;
	}
	
	public function query($query, $parameters = false) {
		return [];
	}

}
