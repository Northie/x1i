<?php

namespace Plugins;

trait helper {

	public function notify($event, $object=false, $options = array()) {
				$object = $object ? $object : $this;
		$event = "on" . ucfirst($event);
		return \Plugins\Plugins::Load()->doPlugins($event, $object, $options);
	}

	public function before($event, $object, $options = array()) {
				$object = $object ? $object : $this;
		$event = "Before" . ucfirst($event);
		return $this->notify($event, $object, $options);
	}

	public function after($event, $object, $options = array()) {
				$object = $object ? $object : $this;
		$event = "After" . ucfirst($event);
		return $this->notify($event, $object, $options);
	}

}