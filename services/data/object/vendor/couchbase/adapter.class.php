<?php

namespace services\data\object\vendor\couchbase;

class adapter extends \services\data\adapter {

	private $couchbase;
	
	public function __construct($settings) {
		if(class_exists("\\CouchbaseCluster",false)) {
                        
                        $host       = $settings['host'];
                        $port       = $settings['port'];
                        $user       = $settings['user'];
                        $password   = $settings['pass'];
                        $bucket     = $settings['name'];
                        
                        //$this->couchbase = new \Couchbase($host.":".$port,$user,$password,$bucket);
                        
                        
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

	public function create($data, $id = false) {
            
                $key = $id;
            
		return $this->couchbase->upsert($key, $data);
	}

	public function read($key) {
                try {
                    $rs = $this->couchbase->get($key);
                    
                    return \utils\Tools::object2array($rs->value);
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
            
                $key = $conditions;
            
		$exists = 0;

		if ($this->read($key)) {
			$exists = 1;
                        
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
            $query = \CouchbaseN1qlQuery::fromString($query);
            if(is_array($parameters)){
                $query->namedParams($parameters);
            }
            
            $r = new \ReflectionObject($this->couchbase);
            
            $result = $this->couchbase->query($query);
            return $result;
        }
}
