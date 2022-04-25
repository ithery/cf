<?php

class CRouting_Manager {
    protected $uriRoutings;

    protected $defaultRoute;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->uriRoutings = CF::config('routes');
        $this->defaultRoute = carr::get($this->uriRoutings, '_default');
    }

    public function addUriRouting($uri, $routedUri) {
        $this->uriRoutings[$uri] = $routedUri;
    }

    public function getUriRoutings() {
        return $this->uriRoutings;
    }

    public function getDefaultRoute() {
        return $this->defaultRoute;
    }
}
