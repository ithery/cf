<?php

class CGeo_Geocoder {
    /**
     * @var CGeo_ProviderAggregator
     */
    private $providerAggregator;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct() {
        $this->providerAggregator = new CGeo_ProviderAggregator();
    }
}
