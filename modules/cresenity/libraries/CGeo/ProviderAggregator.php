<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:24:53 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGeo_ProviderAggregator implements CGeo_Interface_GeocoderInterface {

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

    /**
     * @param callable|null $decider
     * @param int           $limit
     */
    public function __construct(callable $decider = null, $limit = CGeo_Interface_GeocoderInterface::DEFAULT_RESULT_LIMIT) {
        $this->limit = $limit;
        $this->decider = $decider != null ? $decider : __CLASS__ . '::getProvider';
    }

    /**
     * {@inheritdoc}
     */
    public function geocodeQuery(CGeo_Query_GeocodeQuery $query) {
        if (null === $query->getLimit()) {
            $query = $query->withLimit($this->limit);
        }
        return call_user_func($this->decider, $query, $this->providers, $this->provider)->geocodeQuery($query);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseQuery(CGeo_Query_ReverseQuery $query) {
        if (null === $query->getLimit()) {
            $query = $query->withLimit($this->limit);
        }
        return call_user_func($this->decider, $query, $this->providers, $this->provider)->reverseQuery($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'provider_aggregator';
    }

    /**
     * {@inheritdoc}
     */
    public function geocode($value) {
        return $this->geocodeQuery(CGeo_Query_GeocodeQuery::create($value)
                                ->withLimit($this->limit));
    }

    /**
     * {@inheritdoc}
     */
    public function reverse($latitude, $longitude) {
        return $this->reverseQuery(CGeo_Query_ReverseQuery::create(new CGeo_Model_Coordinates($latitude, $longitude))
                                ->withLimit($this->limit));
    }

    /**
     * Registers a new provider to the aggregator.
     *
     * @param Provider $provider
     *
     * @return ProviderAggregator
     */
    public function registerProvider(CGeo_Interface_ProviderInterface $provider) {
        $this->providers[$provider->getName()] = $provider;
        return $this;
    }

    /**
     * Registers a set of providers.
     *
     * @param Provider[] $providers
     *
     * @return ProviderAggregator
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
     * @param CGeo_Query_GeocodeQuery|CGeo_Query_ReverseQuery   $query
     * @param CGeo_Interface_ProviderInterface[]                $providers
     * @param CGeo_Interface_ProviderInterface                  $currentProvider
     *
     * @return CGeo_Interface_ProviderInterface
     *
     * @throws CGeo_Exception_ProviderNotRegistered
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
