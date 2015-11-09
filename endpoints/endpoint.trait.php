<?php
namespace endpoints;

trait endpoint {
	protected $data = [];
	protected $appliedFilters = [];
	
	public function Init($request,$response,$filters) {
		$this->request = $request;
		$this->response = $response;
		$this->filters = $filters;
	}
	public function getNamedFilterList() {
		return $this->filters;
	}
	protected function filterInsertBefore($filter, $before) {
		$newList = [];
		foreach ($this->filters as $i=> $filterName) {
			$newList[] = $filterName;
			if ($before == $this->filters[$i + 1]) {
				$newList[] = $filter;
			}
		}
		$this->filters = $newList;
	}
	protected function filterInsertAfter($filter, $after) {
		$newList = [];
		foreach ($this->filters as $i=> $filterName) {
			$newList[] = $filterName;
			if ($after == $this->filters[$i]) {
				$newList[] = $filter;
			}
		}
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
}