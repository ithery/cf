<?php

/**
 * @method CGeo_Interface_CollectionInterface reverse(float $latitude, float $longitude, array $data = [])
 * @method CGeo_Interface_CollectionInterface geocode(string $value, array $data = [])
 */
class CGeo_Geocoder {
    use CTrait_ForwardsCalls;

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

    /**
     * @param string     $nominatimRootUrl
     * @param null|mixed $userAgent
     * @param mixed      $extension
     *
     * @return CGeo_Geocoder
     */
    public function withNominatim($nominatimRootUrl, $extension = '', $userAgent = null) {
        if ($userAgent == null) {
            $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36';
        }
        $this->providerAggregator->clearProvider();
        $this->providerAggregator->registerProvider(new CGeo_Provider_Nominatim($this->createHttpClient(), $nominatimRootUrl, $userAgent, $extension))->using('nominatim');

        return $this;
    }

    protected function createHttpClient() {
        $client = new \Http\Adapter\Guzzle6\Client();

        return $client;
    }

    public function __call($method, $parameters) {
        return $this->forwardCallTo($this->providerAggregator, $method, $parameters);
    }
}
