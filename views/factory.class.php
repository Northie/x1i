<?php

namespace views;

class factory {
	 public static function Build($normalisedRequest) {
		 
		if($normalisedRequest['module']) {
			$targetView = "\\views\\modules\\".$normalisedRequest['module']."\\".$normalisedRequest['context']."\\".$normalisedRequest['endpoint'];
		} else {
			$targetView = "\\views\\".$normalisedRequest['context']."\\".$normalisedRequest['endpoint'];
		}
		
		if(class_exists($targetView)) {
			return new $targetView;
		} else {
			return new DefaultView;	
		}
		
	 }
}
