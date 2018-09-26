<?php

namespace services\data\http\client;

class factory {

	public static function Build() {

		$o = new adapter;

		return $o;
	}

}
