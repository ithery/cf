<?php

use Symfony\Component\Finder\Finder;

class CConfig_Manager {
    protected $repository;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->repository = new CConfig_Repository([]);
    }
}
