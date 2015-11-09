<?php

namespace flow;

class Request {
	use \Plugins\helper;

	private $requestKey = '';
	private $ajax = false;
	private $https = false;
	private $dynamic = [];
	private $endpoint = false;
        private $modules = [];

	public function __construct($server = false) {

		if (!$this->before('RequestConstruct', $this)) {
			return false;
		}
                
		if (!$server) {
			//if no $_SERVER then throw exception? should come from cli.php?
			$server = $_SERVER;
		}

		foreach ($server as $key=> $val) {
			$this->__set($key, $val);
		}
		
		$this->__set('server',$server);

		$this->requestKey = uniqid();


		$this->HTTP_SCHEME = 'http://';

		if ($this->HTTPS == 'on') {
			$this->HTTP_SCHEME = 'https://';
			$this->setIsHTTPS();
		}


		$url = $this->HTTP_SCHEME . $this->HTTP_HOST . $this->REQUEST_URI;
		
		$this->__set('URI',$url);

		$request = parse_url($url);

		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->ajax = true;
			$this->notify('DetectingAjax', $this);
		}

		//set requested file extension
		$ext = explode(".", $server['REQUEST_URI']);
		if (count($ext) > 1) {
			$this->ext = array_pop($ext);
		}

		$this->after('RequestConstruct', $this);
	}

        public function setActiveContext($context) {            
            $this->context = $context;
        }

        public function isHTTPS() {
		return $this->https;
	}

	public function setIsHTTPS($set = true) {
		$this->https = $set;
		$this->notify('SetIsHttps', $this);
	}

	public function isAjax() {
		return $this->ajax;
	}

	public function setIsAjax($set = true) {
		$this->ajax = $set;
	}

	public function __get($key) {
		return $this->dynamic[$key];
	}

	public function __set($key, $val) {
		$this->dynamic[$key] = $val;
	}

	public function setEndpoint($endpoint) {

            \settings\registry::Load()->set(['REQUEST', 'CONTEXT'], $this->context);
            \settings\registry::Load()->set(['REQUEST', 'ENDPOINT'], $endpoint);

            $this->ENDPOINT = $endpoint;
            $this->endpoint = $endpoint;
        }

	public function getEndpoint() {
		return $this->endpoint;
	}
	
	public function getRequestType() {
		return $this->requestType;
	}
	
	private function setRequestType($verb) {
		$this->requestType = strtoupper($verb);
	}
        
        public function addModule($module) {
            $this->modules[] = $module;
        }
        
        public function getModules() {
            return $this->modules;
        }
}