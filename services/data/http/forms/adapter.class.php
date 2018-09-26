<?php

namespace services\data\http\form;

class adapter extends \services\data\adapter {
	public function __construct($namespace = '', $method='POST') {
		$this->namespace = $namespace;
		$this->method = $method;
		
		switch($method) {
			case 'POST':
				$data = $_POST;
				break;
			case 'GET':
				$data = $_GET;
				break;
			default:
				$data = [];
		}
		
		if($this->namespace) {
			$data = $data[$this->namespace];
		}
		
		$this->data = $data;
		
	}
	
	public function create($key, $data) {
		
	}

	public function read($key) {
		return $this->data[$key];
	}


	public function update($key, $data) {		
		
	}

	public function delete($key) {

	}
		
		public function readRawData() {
			$fp = fopen("php://input");
			$c = false;
			if($fp) {
				$c = '';
				while($line = fgets($fp)) {
					$c.=$line;
				}
				fclose($fp);
			}
			return $c;
		}
}