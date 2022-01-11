<?php

class CRouting_UrlParser {
    protected $uri;

    public function __construct($uri) {
        $this->uri = $uri;
    }

    public function segments() {
    }

    public function routedUri() {
        // Load routes
        $routes = CFRouter::getRoutes();
    }
}
