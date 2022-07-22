<?php

class CGeo_Geocoder {
    /**
     * @var Provider[]
     */
    private $providers = [];

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var int
     */
    private $limit;

    /**
     * A callable that decided what provider to use.
     *
     * @var callable
     */
    private $decider;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
