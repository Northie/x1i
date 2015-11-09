<?php

namespace flow\filters;

trait filter {
	
	use \Plugins\helper;

	private $currentNode;
	private $list;
	private $request;
	private $response;

	public function __construct($list, $request, $response) {
		$this->list = $list;
		$this->request = $request;
		$this->response = $response;
	}

	public function init() {
		//as this is called just after the filter has been stacked, the list object has this object as the last node
		$this->currentNode = $this->list->getLastNode();
		$this->request->getEndpoint()->filteredBy($this);
	}

	private function getNext() {
		if ($this->currentNode->next->label) {
			return $this->list->getNodeValue($this->currentNode->next->label);
		}
		return false;
	}

	private function getPrev() {
		if ($this->currentNode->previous->label) {
			return $this->list->getNodeValue($this->currentNode->previous->label);
		}
		return false;
	}

	public function FFW() {
		$filter = $this->getNext();

		if ($filter) {
			$filter->in();
		} else {
			$this->out();
		}
	}

	public function RWD() {
		$filter = $this->getPrev();
		if ($filter) {
			$filter->out();
		}
	}

}