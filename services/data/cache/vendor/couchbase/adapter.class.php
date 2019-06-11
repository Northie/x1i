<?php

namespace services\data\cache\vendor\couchbase;

class adapter extends \services\data\adapter {

	use \services\data\cache\cache;
	
	private $couchbase;
	
	public function __construct($settings) {
		if(class_exists("\\CouchbaseCluster",false)) {
						
			$host		= $settings['host'];
			$port		= $settings['port'];
			$user		= $settings['user'];
			$password   = $settings['pass'];
			$bucket	 	= $settings['name'];
			
			$authenticator = new \Couchbase\PasswordAuthenticator();
			$authenticator->username($user)->password($password);

			$cluster = new \Couchbase\Cluster('couchbase://'.$host);
			$cluster->authenticate($authenticator);
			
			//$cluster = new \CouchbaseCluster("couchbase://".$host."/".$user);
			$this->couchbase = $cluster->openBucket($bucket);
			
		} else {
			throw new \services\data\cacheException('Couchbase not enabled');
		}
	}

	public function create($data,$id = false) {
			
		$key = X1_APP_NAME.'-'.$id;

		if($lifetime) {
			$expires = time() + $lifetime;
		} else {
			$expires = time() + $this->getLifetime();
		}
	
		$meta = ['expires'=>$expires];
			
		$data = [
					 'meta'=>$meta
					,'data'=>$data
				];
		
		
		try {
			$rs = $this->couchbase->upsert($key, $data);
		} catch (\Exception $e) {
			$rs = false;
		} finally {
			return $rs;
		}
		
		
	}

	public function read($key) {
		$key = X1_APP_NAME.'-'.$key;
	
		try {
			$rs = $this->couchbase->get($key);
			
			$data = \utils\Tools::object2array($rs->value);

			if(isset($data['meta']['expires']) && $data['meta']['expires'] < time()) {
				//cleanup
				$this->delete($key,true);
				return [];
			}

			return isset($data['data']) ? $data['data'] : $data;
		} catch (\Exception $e) {
			return [];
		}
	}

	/**	 *
	 * @param string $key
	 * @param mixed $data
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 * @desc matching apc user cache behaviour
	 */
	public function update($data, $conditions = false) {

		$key = $key = X1_APP_NAME.'-'.$conditions;
				
		$exists = 0;

		if ($this->read($key)) {
			$exists = 1;
						
			if($lifetime) {
				$expires = time() + $lifetime;
			} else {
				$expires = time() + $this->getLifetime();
			}

			$meta = ['expires'=>$expires];

			$data = json_encode([
				 'meta'=>$meta
				,'data'=>$data
			]);
						
			$rs = $this->couchbase->upsert($key, $data);
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
	public function delete($key,$force=false) {
		$exists = 0;
				$key = X1_APP_NAME.'-'.$key;
				if($force) {
					$rs = $this->couchbase->remove($key);
				} else {

					if ($this->read($key)) {
							$exists = 1;
							$rs = $this->couchbase->remove($key);
							if (!$rs) {
									$exists = -1;
							}
					}
				}

		return $exists;
	}
		
	public function query($query,$parameters=false) {
		$query = CouchbaseN1qlQuery::fromString($query);
		if(is_array($parameters)){
			$query->namedParams($parameters);
		}
		$result = $bucket->query($query);
		return $result;
	}
}
