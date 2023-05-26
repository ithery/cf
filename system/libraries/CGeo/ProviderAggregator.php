<?php

defined('SYSPATH') or die('No direct access allowed.');

class CGeo_ProviderAggregator implements CGeo_Interface_GeocoderInterface {
    /**
     * @var CGeo_Interface_ProviderInterface[]
     */
    private $providers = [];

    /**
     * @var CGeo_Interface_ProviderInterface
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

    /**
     * @param null|callable $decider
     * @param int           $limit
     */
    public function __construct(callable $decider = null, $limit = CGeo_Interface_GeocoderInterface::DEFAULT_RESULT_LIMIT) {
        $this->limit = $limit;
        $this->decider = $decider != null ? $decider : __CLASS__ . '::getProvider';
    }

    /**
     * @inheritdoc
     */
    public function geocodeQuery(CGeo_Query_GeocodeQuery $query) {
        if (null === $query->getLimit()) {
            $query = $query->withLimit($this->limit);
        }

        return call_user_func($this->decider, $query, $this->providers, $this->provider)->geocodeQuery($query);
    }

    /**
     * @inheritdoc
     */
    public function reverseQuery(CGeo_Query_ReverseQuery $query) {
        if (null === $query->getLimit()) {
            $query = $query->withLimit($this->limit);
        }

        return call_user_func($this->decider, $query, $this->providers, $this->provider)->reverseQuery($query);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'provider_aggregator';
    }

    /**
     * @inheritdoc
     */
    public function geocode($value, $data = []) {
        $geocodeQuery = CGeo_Query_GeocodeQuery::create($value)
            ->withLimit($this->limit);
        foreach ($data as $key => $value) {
            $geocodeQuery = $geocodeQuery->withData($key, $value);
        }

        return $this->geocodeQuery($geocodeQuery);
    }

    /**
     * @inheritdoc
     */
    public function reverse($latitude, $longitude, $data = []) {
        $reverseQuery = CGeo_Query_ReverseQuery::create(new CGeo_Model_Coordinates($latitude, $longitude))
            ->withLimit($this->limit);
        foreach ($data as $key => $value) {
            $reverseQuery = $reverseQuery->withData($key, $value);
        }

        return $this->reverseQuery($reverseQuery);
    }

    /**
     * Registers a new provider to the aggregator.
     *
     * @param CGeo_Interface_ProviderInterface $provider
     *
     * @return CGeo_ProviderAggregator
     */
    public function registerProvider(CGeo_Interface_ProviderInterface $provider) {
        $this->providers[$provider->getName()] = $provider;

        return $this;
    }

    /**
     * Registers a set of providers.
     *
     * @param CGeo_Interface_ProviderInterface[] $providers
     *
     * @return CGeo_ProviderAggregator
     */
    public function registerProviders(array $providers = []) {
        foreach ($providers as $provider) {
            $this->registerProvider($provider);
        }

        return $this;
    }

    /**
     * Sets the default provider to use.
     *
     * @param string $name
     *
     * @return CGeo_ProviderAggregator
     */
    public function using($name) {
        if (!isset($this->providers[$name])) {
            throw CGeo_Exception_ProviderNotRegistered::create($name != null ? $name : '', $this->providers);
        }
        $this->provider = $this->providers[$name];

        return $this;
    }

    /**
     * Clear registered providers.
     *
     * @return CGeo_ProviderAggregator
     */
    public function clearProvider() {
        $this->providers = [];

        return $this;
    }

    /**
     * Returns all registered providers indexed by their name.
     *
     * @return CGeo_Interface_ProviderInterface[]
     */
    public function getProviders() {
        return $this->providers;
    }

    /**
     * Get a provider to use for this query.
     *
     * @param CGeo_Query_GeocodeQuery|CGeo_Query_ReverseQuery $query
     * @param CGeo_Interface_ProviderInterface[]              $providers
     * @param CGeo_Interface_ProviderInterface                $currentProvider
     *
     * @throws CGeo_Exception_ProviderNotRegistered
     *
     * @return CGeo_Interface_ProviderInterface
     */
    private static function getProvider($query, array $providers, CGeo_Interface_ProviderInterface $currentProvider = null) {
        if (null !== $currentProvider) {
            return $currentProvider;
        }
        if (0 === count($providers)) {
            throw CGeo_Exception_ProviderNotRegistered::noProviderRegistered();
        }
        // Take first
        $key = key($providers);

        return $providers[$key];
    }
}
