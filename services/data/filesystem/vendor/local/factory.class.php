<?php

namespace services\data\filesystem\vendor\local;

class factory {

	public static function Build($settings) {
		$o = new adapter($settings);
		return $o;
	}

}
