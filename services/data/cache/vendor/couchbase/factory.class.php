<?php

namespace services\data\cache\vendor\couchbase;

class factory {

	public static function Build() {
		$o = new adapter;
		return $o;
	}

}
