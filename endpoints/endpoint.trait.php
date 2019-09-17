<?php
namespace endpoints;

trait endpointHelper {
	protected $data = [];
	protected $appliedFilters = [];
	protected $executable = [];
	
	public function Init($request,$response,$filters) {
		$this->request = $request;
		$this->response = $response;
		$this->filters = $filters;
		
		$this->setExecutable($this,'Execute');

		$nr = $request->getNormalisedRequest();
		
		$this->modelName = '';
		
		if($nr['module']) {
			$this->modelName.=$nr['module']."_";
		}
		
		$this->modelName.=$nr['endpoint'];
				
	}
		
	public function getNamedFilterList() {
		return $this->filters;
	}

		
	public function getFilterOptions($filter) {
		return $this->filterOptions[$filter];
	}


	public function filterInsertBefore($filter, $before,$options=[]) {
		$newList = [];
		foreach ($this->filters as $i=> $filterName) {
			$newList[] = $filterName;
			if ($before == $this->filters[$i + 1]) {
				$newList[] = $filter;
			}
		}
				$this->filterOptions[$filter] = $options;
		$this->filters = $newList;
	}
		
	public function filterInsertAfter($filter, $after,$options=[]) {
		$newList = [];
		foreach ($this->filters as $i=> $filterName) {
			$newList[] = $filterName;
			if ($after == $this->filters[$i]) {
				$newList[] = $filter;
			}
		}
				$this->filterOptions[$filter] = $options;
		$this->filters = $newList;
	}

	public function getData() {
		return $this->data;
	}
	
	public function filteredBy($filter) {
		$this->appliedFilters[] = $filter;
	}
	
	public function getAppliedFilters() {
		return $this->appliedFilters;
	}

		
	public function Execute() {
		
		$action = key($this->request->getQuery()['path']);
		
		$action = $action ? $action : 'index';
		
		if(method_exists($this, $action)) {
			if($this->before(__CLASS__."::".$action,$this)) {
				$this->notify(__CLASS__."::".$action,$this);
				$this->{$action}();
			}
			$this->after(__CLASS__."::".$action,$this);
		} else {
			$this->notify('EndpointActionNotFound');
		}
	
	}
		
	public function addData($key,$value) {
		$this->data[$key] = $value;
	}
	
	public function isModule() {
		$r = new \ReflectionObject($this);
		var_dump($r->getNamespaceName());
	}
	
	public function getModule() {
		$r = new \ReflectionObject($this);
		var_dump($r->getNamespaceName());
	}

	public function getExecutable() {
		return $this->executable;
	}

	public function setExecutable($object,$method) {
		$this->executable = [$object,$method];
	}

}