<?php

namespace endpoints;

class factory {
	public static function Build($endpoint,$request,$response,$filters) {
		$ep = new $endpoint($request,$response,$filters);
		\Plugins\EventManager::Load()->ObserveEvent('endpointCreated', $ep);
		return $ep;
	}
}