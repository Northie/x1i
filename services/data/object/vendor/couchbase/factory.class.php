<?php

namespace services\data\object\vendor\couchbase;

class factory {

	public static function Build($settings) {
		$o = new adapter($settings);
		return $o;
	}

}
