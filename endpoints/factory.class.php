<?php

namespace endpoints;

class factory {
    public static function Build($endpoint,$request,$response,$filters) {
        return new $endpoint($request,$response,$filters);
    }
}