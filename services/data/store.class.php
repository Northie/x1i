<?php

namespace services\data;

class store {

        private $proxy;
        private $model;
	private $data;
        private $new = [];
        private $updates = [];
        private $deletes = [];

	public function __construct($model) {
		$this->setModel($model);
	}

	public function setModel(\models\data\model $model) {
		$this->model = $model;
		$model->setStore($this);
		return $this;
	}

        public function load($id=null, $options = []) {
                \Plugins\EventManager::Load()->ObserveEvent('onBeforeLoad', $this, ['id'=>$id]);
                if($id) {
                        $data = $this->proxy->read($id);

                        if($this->model->getType() == $data['type']) {
                                $this->id = $id;
                                $this->data[$id] = $this->instantiate($data);
                        } else {
				
				$frontController = \settings\registry::Load()->get('FrontController');
				$frontController->response->respond(["HTTP/1,1 404 Not Found"]);
				
				if(!$data) {
					throw new \Exception('Resource not found');
				} else {
					throw new \Exception('Resoure ID and Type Mis Match. Resource should be of type '.$this->model->getType().', identifer returns type of '.$data['type']);
				}

			}
		} else {
                        if(!$this->data) {
                                //if no data, get all, no looping
                                $rows = $this->proxy->readType($this->model->getType(), $options);
                                foreach($rows as $id => $row) {
                                        $this->data[$id] = $this->instantiate($row);
                                }
                        } else {
                                //if data, loop to append
                                foreach ($this->proxy->readType($this->model->getType(), $options) as $id => $row) {
                                        $this->data[$id] = $this->instantiate($row);
                                }
                        }
                }
                \Plugins\EventManager::Load()->ObserveEvent('onLoad', $this, ['id'=>$id]);
                return $this;
        }

	public function addItem($item) {		
		
		$item = $this->integrate($item);
		
                if(is_a($this->data,'generator')) {
                        $this->data = [];
                        foreach ($this->proxy->readType($this->model->getType()) as $id => $row) {
                                $this->data[$id] = $this->instantiate($row);
                        }
                }

                $this->data[$item[$this->model->idParam]] = $this->instantiate($item);

		$this->new[] = $item[$this->model->idParam];
		
		return $this;
	}
	
        public function updateItem($item) {
                $this->data[$item[$this->model->idParam]] = $this->instantiate($this->integrate($item,$this->data[$item[$this->model->idParam]]));

                $this->updates[] = $item[$this->model->idParam];

                return $this;
        }
	
	public function destroyItem($item) {
		$id = $item[$this->model->idParam];
		unset($this->data[$item[$this->model->idParam]]);
		$this->deletes[] = $id;
		return $this;
	}

	public function getAll($generate=false) {
		
		if(is_a($this->data, 'Generator') && $generate) {
			$data = [];
			foreach($this->data as $key => $val) {
				$data[$key] = $val;
			}
			return $data;
		}
		
		return $this->data;
	}

	public function saveAll() {
		$this->saveNew();
		$this->saveUpdates();
		$this->commitDeletes();
	}
	
	public function saveNew() {	
		
		$errors = 0;
		try {
			foreach ($this->new as $id) {
                                $this->proxy->create($this->data[$id],$id);
				unset($this->new[$id]);
			}
		} catch (\Exception $e) {
			$errors++;
		} finally {
			return !$errors;
		}
	}
	
	public function saveUpdates() {
		$errors = 0;
		try {
			foreach ($this->updates as $id) {				
                                $this->proxy->update($this->data[$id],$id);
				unset($this->updates[$id]);
			}
		} catch (\Exception $e) {
			$errors++;
		} finally {
			return !$errors;
		}
	}
	
	public function commitDeletes() {
		$errors = 0;
		try {
			foreach ($this->deletes as $id) {				
                                $this->proxy->delete($id,true);
				unset($this->deletes[$id]);
			}
		} catch (\Exception $e) {
			$errors++;
		} finally {
			return !$errors;
		}
	}

        public function sync() {
                \Plugins\EventManager::Load()->ObserveEvent('onBeforeSync', $this);
                $this->saveAll();
                $this->load();
                \Plugins\EventManager::Load()->ObserveEvent('onSync', $this);
        }
	
	public function getOne($id=false) {
		$id = $id ? $id : $this->id;
		if(!$id) {
			throw new \Exception('No resource ID supplied');
		}
		return $this->data[$id];
	}

	public function getModel() {
		return $this->model;
	}

        public function setProxy(\services\data\proxy $proxy) {
                $this->proxy = $proxy;
                $this->model->setProxy($proxy);
                return $this;
        }

        public function getProxy() {
                return $this->proxy;
        }

        public function integrate($src,$dest=[]) {

		$structure = $this->model->getStructure();
		
		$src['type'] = $src['type'] ? $src['type'] : $this->model->getType();
		
		foreach($structure as $field => $properties) {
			if(!$src[$field] && $properties[2]) {
				$dest[$field] = isset($dest[$field]) ? $dest[$field] : \call_user_func_array($properties[2],[$src]);
			}
			if(isset($src[$field])) {
				$dest[$field] = $src[$field];
			} else {
				if(!$dest[$field]) {
					$dest[$field] = $properties[3] ? $properties[3] : null;
				}
			}
			unset($src[$field]);
		}
		
		foreach($src as $key => $val) {
			$dest['additional'][$key] = $val;
		}		
		//recurse through data, put matching keys from model structire into item data and any other values in an attributes array??
		
                return $dest;
        }

        private function instantiate($data) {
                $cls = get_class($this->model);
                $model = new $cls();
                foreach ($data as $k => $v) {
                        $model->$k = $v;
                }
                $model->setStore($this);
                if($this->proxy) {
                        $model->setProxy($this->proxy);
                }
                return $model;
        }

}
