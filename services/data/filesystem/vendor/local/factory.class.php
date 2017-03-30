<?php

namespace services\data\filesystem\vendor\local;

class factory {

	public static function Build($namespace) {
		$o = new adapter($namespace);
		return $o;
	}

}
