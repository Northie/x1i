<?php

namespace flow\filters;

class actionFilter {
	use filter;

	public function in() {

		///*
		
		list($object,$method) = $this->request->getEndpoint()->getExecutable(); //usually defined as [$this,'Execute']
		$object->{$method}();

		//*/

		//$this->request->getEndpoint()->Execute();

		$this->FFW();
	}

	public function out() {

		$this->response->setData($this->request->getEndpoint()->getData());

		$this->RWD();
	}

}