<?php

namespace services\data\cache\vendor\couchbase;

class adapter extends \services\data\adapter {

	private $couchbase;
	
	
	public function __construct() {
		if(class_exists("\\Couchbase")) {
			$this->couchbase = new \Couchbase("localhost:8091","x1appcache","x1appcachepassword","x1appcache");
		} else {
			throw new \services\data\cacheException('Couchbase not enabled');
		}
	}

	public function create($key, $data) {
		$data = json_encode($data);
		return $this->couchbase->set($key, $data);
	}

	public function read($key) {
		$data = $this->couchbase->get($key);
		return json_decode($data,1);
	}

	/**
	 * APC Update - updates the cache with new data
	 *
	 * This function will NOT create a new cache entry if the key does not exist. Use create instead
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 * @desc
	 */
	public function update($key, $data) {
		$exists = 0;

		if ($this->read($key)) {
			$exists = 1;
			$rs = $this->couchbase->replace($key, $data);
			if (!$rs) {
				$exists = -1;
			}
		}


		return $exists;
	}

	/**
	 *
	 * @param string $key
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 */
	public function delete($key) {
		$exists = 0;

		if ($this->read($key)) {
			$exists = 1;
			$rs = $this->couchbase->delete($key);
			if (!$rs) {
				$exists = -1;
			}
		}

		return $exists;
	}

}
