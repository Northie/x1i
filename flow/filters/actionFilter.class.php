<?php

namespace flow\filters;

class actionFilter {
	use filter;

	public function in() {

		$this->request->getEndpoint()->Execute();

		$this->FFW();
	}

	public function out() {

		$this->response->setData($this->request->getEndpoint()->getData());

		$this->RWD();
	}

}