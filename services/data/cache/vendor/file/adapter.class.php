<?php

namespace services\data\cache\vendor\file;

class adapter extends \services\data\adapter {

	private $path;
        private $dir = 'cache';
        private $prefix = 'X1-cache-';
        private $suffix = '.json';
	
	
	public function __construct($settings) {
	    $this->path = $settings['path'];
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
                
                //file put contents
		return $this->couchbase->set($key, $data);
	}

	public function read($key) {
            
		//$json = $this->couchbase->get($key);
                //file get contents
                
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
            return $this->create($key, $data,$lifetime=false);
	}

	/**
	 *
	 * @param string $key
	 * @return int; 1 for success, 0 for didn't exist, nothing to do and -1 for failed to delete existing key
	 */
	public function delete($key,$force=false) {
		$exists = 0;
                //unlink file
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
