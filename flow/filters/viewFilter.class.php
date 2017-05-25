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

                if($this->normalisedRequest['path']) {
                    $view->setPath($this->normalisedRequest['path']);
                }
                
                $view->setData($data);
                
                $view->serve();
                
		$this->RWD();
	}

}