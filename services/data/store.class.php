<?php

namespace services\data;

class store {

	private $reader;
	private $writer;
	private $model;
	private $data;
	private $new = [];
	private $updates = [];

	public function __construct($model) {
		$this->setModel($model);
	}

	public function setModel(\models\data\model $model) {
		$this->model = $model;
		$model->setStore($this);
		return $this;
	}

	public function load($id=null) {
		if($id) {
			$this->data[$id] = $this->reader->read($id);
		} else {
			if(!$this->data) {
				//if no data, get all, no looping
				$this->data = $this->reader->readType($this->model::$type);
			} else {
				//if data, loop to append
				foreach ($this->reader->readType($this->model::$type) as $id => $row) {
					$this->data[$id] = $row;
				}
			}
		}
		return $this;
	}

	public function addItem($item) {

		$item = $this->integrate($item);
		
		$this->data[$item[$this->model->idParam]] = $item;
		
		
		$this->new[] = $item[$this->model->idParam];
		
		return $this;
	}
	
	public function updateItem($item) {
		$this->data[$item[$this->model->idParam]] = $this->integrate($item);
		$this->updates[] = $item[$this->model->idParam];
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
	}
	
	public function saveNew() {	
		foreach ($this->new as $id) {
			
			$this->writer->create($this->data[$id],$id);
		}
	}
	
	public function saveUpdates() {
		foreach ($this->updates as $id) {
			$this->writer->update($id,$this->data[$id]);
		}		
	}
	
	public function sync() {
		$this->saveAll();
		$this->load();
	}
	
	public function getOne($id) {
		$model = \modelFactory::Build(\get_class($this->model));
		$model->setData($this->data[$id]);
		return $model;
	}

	public function getModel() {
		return $this->model;
	}

	public function setWriter(\services\data\iAdapter $writer) {
		$this->writer = $writer;
		return $this;
	}

	public function setReader(\services\data\iAdapter $reader) {
		$this->reader = $reader;
		return $this;
	}
	
	/**
	 * @return dataservice
	 */
	
	public function getWriter() {
		return $this->writer;
	}
	
	/**
	 * @return dataservice
	 */
	public function getReader() {
		return $this->reader;
	}

	public function integrate($data) {
		
		$structure = $this->model->getStructure();
		
		$item = [];
		
		foreach($structure as $field => $properties) {
			if(!$data[$field] && $properties[2]) {
				$data[$field] = \call_user_func($properties[2]);
			}
			
			$item[$field] = $data[$field];
			unset($data[$field]);
		}
		
		foreach($data as $key => $val) {
			$item['additional'][$key] = $val;
		}
		
		//recurse through data, put matching keys from model structire into item data and any other values in an attributes array??
		
		return $item;
	}
	
}
