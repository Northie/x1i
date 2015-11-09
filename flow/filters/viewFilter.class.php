<?php

namespace flow\filters;

class viewFilter {
	use filter;

	public function in() {

		$this->FFW();
	}

	public function out() {
		$data = $this->response->getData();

		if (is_null($this->request->ext)) {
			if ($this->request->isAjax()) {
				$renderer = 'JSON';
			} else {
				$renderer = 'HTML';
			}
		} else {
			$renderer = strtoupper($this->request->ext);
		}

		var_dump($renderer);
		var_dump($data);

		$this->RWD();
	}

}