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

	public function create($key, $data,$lifetime=false) {
            
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
                
		return $this->couchbase->set($key, $data);
	}

	public function read($key) {
            
		$json = $this->couchbase->get($key);
                
                $data = json_decode($json,1);
                
                if(isset($data['meta']['expires']) && $data['meta']['expires'] < time()) {
                    //cleanup
                    $this->delete($key,true);
                    return [];
                }
                
		return isset($data['data']) ? $data['data'] : $data;
	}

	/**	 *
	 * @param string $key
	 * @param mixed $data
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 * @desc matching apc user cache behaviour
	 */
	public function update($key, $data,$lifetime=false) {
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
	public function delete($key,$force=false) {
		$exists = 0;

                if($force) {
                    $rs = $this->couchbase->delete($key);
                } else {

                    if ($this->read($key)) {
                            $exists = 1;
                            $rs = $this->couchbase->delete($key);
                            if (!$rs) {
                                    $exists = -1;
                            }
                    }
                }

		return $exists;
	}

        private function getLifetime() {
            if(($cacheLifetime = \settings\general::Load()->get(['CACHE_LIFETIME']))) {
                return $cacheLifetime;
            }
            return 3600;
        }
}
