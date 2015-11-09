<?php

namespace libs\factory;

trait flow {

	private $currentNode;
	private $list;
	private $parent;
	private $controller;
	public $options = [];

	public function __construct($list, $parent, $controller) {
		$this->list = $list;
		$this->parent = $parent; //top level factory
		$this->controller = $controller;
	}

	public function init() {
		$this->currentNode = $this->list->getLastNode();
		//$this->request->getEndpoint()->filteredBy($this);
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

		if (connection_aborted()) {
			$filter = $this->unbuild();
		} else {

			$filter = $this->getNext();

			if ($filter) {
				$filter->build();
			}
		}
	}

	public function RWD() {
		$filter = $this->getPrev();
		if ($filter) {
			$filter->unbuild();
		}
	}

	public function success() {
		if (method_exists($this->parent, 'success')) {
			$this->parent->success($this->currentNode->label);
		}

		$this->FFW();
	}

	public function failed() {
		if (method_exists($this->parent, 'failed')) {
			$this->parent->failed($this->currentNode->label);
		}

		$this->Unbuild();
	}

	public function setOptions($options) {
		$this->options = $options;
	}

}
