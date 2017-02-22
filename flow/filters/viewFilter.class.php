<?php

namespace flow\filters;

class viewFilter {
	use filter;

	public function in() {
            
            $this->normalisedRequest = $this->request->getNormalisedRequest();
            
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

                $view = \views\factory::Build($this->normalisedRequest);

                $view->setData($data);
                
                $view->serve();
                
		$this->RWD();
	}

}