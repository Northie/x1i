<?php

namespace services\data\blackhole\vendor\x1;

class factory {

	public static function Build($settings) {
		$o = new adapter($settings);
		return $o;
	}

}
