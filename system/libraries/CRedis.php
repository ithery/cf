<?php

class CRedis implements CRedis_FactoryInterface {
    /**
     * The Redis server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The name of the default driver.
     *
     * @var string
     */
    protected $driver;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The Redis connections.
     *
     * @var mixed
     */
    protected $connections;

    /**
     * Indicates whether event dispatcher is set on connections.
     *
     * @var bool
     */
    protected $events = false;

    private static $instance;

    /**
     * @return CRedis
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct() {
        $this->driver = 'phpredis';

        $config = CF::config('database.redis');

        $this->config = $config;
    }

    /**
     * Get a Redis connection by name.
     *
     * @param null|string $name
     *
     * @return CRedis_AbstractConnection
     */
    public function connection($name = null) {
        $name = $name ?: 'default';
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        return $this->connections[$name] = $this->configure(
            $this->resolve($name),
            $name
        );
    }

    /**
     * Resolve the given connection by name.
     *
     * @param null|string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return CRedis_AbstractConnection
     */
    public function resolve($name = null) {
        $name = $name ?: 'default';
        $options = $this->getConfig('options') ?: [];
        $config = $this->getConfig($name);
        if (is_array($config)) {
            return $this->connector()->connect(
                $this->parseConnectionConfiguration($config),
                array_merge(carr::except($options, 'parameters'), ['parameters' => carr::get($options, 'parameters.' . $name, carr::get($options, 'parameters', []))])
            );
        }
        $clusterConfig = $this->getConfig('clusters.' . $name);
        if ($clusterConfig) {
            return $this->resolveCluster($name);
        }

        throw new InvalidArgumentException("Redis connection [{$name}] not configured.");
    }

    /**
     * Resolve the given cluster connection by name.
     *
     * @param string $name
     *
     * @return CRedis_AbstractConnection
     */
    protected function resolveCluster($name) {
        return $this->connector()->connectToCluster(
            array_map(function ($config) {
                return $this->parseConnectionConfiguration($config);
            }, $this->getConfig('clusters.' . $name)),
            $this->getConfig('clusters.options') ?: [],
            $this->getConfig('options') ?: []
        );
    }

    /**
     * Configure the given connection to prepare it for commands.
     *
     * @param CRedis_AbstractConnection $connection
     * @param string                    $name
     *
     * @return CRedis_AbstractConnection
     */
    protected function configure(CRedis_AbstractConnection $connection, $name) {
        $connection->setName($name);

        $connection->setEventDispatcher(CEvent::dispatcher());

        return $connection;
    }

    /**
     * Get the connector instance for the current driver.
     *
     * @return CRedis_AbstractConnector
     */
    protected function connector() {
        $customCreator = isset($this->customCreators[$this->driver]) ? $this->customCreators[$this->driver] : null;
        if ($customCreator) {
            return $customCreator();
        }
        switch ($this->driver) {
            case 'predis':
                return new CRedis_Connector_PredisConnector();
            case 'phpredis':
                return new CRedis_Connector_PhpRedisConnector();
        }
    }

    /**
     * Parse the Redis connection configuration.
     *
     * @param mixed $config
     *
     * @return array
     */
    protected function parseConnectionConfiguration($config) {
        $parsed = (new CDatabase_ConfigurationUrlParser())->parseConfiguration($config);
        $driver = strtolower($parsed['driver'] ?? '');
        if (in_array($driver, ['tcp', 'tls'])) {
            $parsed['scheme'] = $driver;
        }

        return array_filter($parsed, function ($key) {
            return !in_array($key, ['driver'], true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function connections() {
        return $this->connections;
    }

    /**
     * Enable the firing of Redis command events.
     *
     * @return void
     */
    public function enableEvents() {
        $this->events = true;
    }

    /**
     * Disable the firing of Redis command events.
     *
     * @return void
     */
    public function disableEvents() {
        $this->events = false;
    }

    /**
     * Set the default driver.
     *
     * @param string $driver
     *
     * @return void
     */
    public function setDriver($driver) {
        $this->driver = $driver;
    }

    /**
     * Disconnect the given connection and remove from local cache.
     *
     * @param null|string $name
     *
     * @return void
     */
    public function purge($name = null) {
        $name = $name ?: 'default';

        unset($this->connections[$name]);
    }

    /**
     * @return string
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string   $driver
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback) {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Pass methods onto the default Redis connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->connection()->{$method}(...$parameters);
    }

    public function getConfig($key, $default = null) {
        return CDatabase::manager()->getConfig('redis.' . $key, $default);
    }
}
