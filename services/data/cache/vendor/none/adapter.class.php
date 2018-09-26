<?php

namespace services\data\cache\vendor\none;

class adapter extends \services\data\adapter {

	public function __construct($settings) {

	}

	public function create($key, $data,$lifetime=false) {
			return true;
	}

	public function read($key) {
			return false;
	}

	public function update($key, $data,$lifetime=false) {
			return $this->create($key, $data,$lifetime=false);
	}

	
	public function delete($key,$force=false) {
			return true;
	}

		private function getLifetime() {
			if(($cacheLifetime = \settings\general::Load()->get(['CACHE_LIFETIME']))) {
				return $cacheLifetime;
			}
			return 3600;
		}
}
