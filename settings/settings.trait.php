<?php

namespace settings;

trait _settings {

	public function set($key, $value) {
		if (is_array($key)) {
			$this->settings[$key[0]][$key[1]] = $value;
		} else {
			$this->settings[$key] = $value;
		}
	}

	public function get($key1 = false, $key2 = false) {
		if ($key1) {

			if ($key2) {
				return $this->settings[$key1][$key2];
			}

			return $this->settings[$key1];
		}

		return $this->settings;
	}

}
