<?php

namespace endpoints;

class factory {
    public static function Build($endpoint,$request,$response,$filters) {
        $ep = new $endpoint($request,$response,$filters);
        \Plugins\Plugins::Load()->DoPlugins('endpointCreated', $ep);
        return $ep;
    }
}