<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CRedis implements CRedis_FactoryInterface {

    private static $instance;

    /**
     * The Redis server configurations.
     *
     * @var array
     */
    protected $config;
    protected $configName;

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

    public static function instance($configName = 'redis') {
        if (self::$instance == null) {
            self::$instance = [];
        }
        if (!isset(self::$instance[$configName])) {
            self::$instance[$configName] = new static($configName);
        }
        return self::$instance[$configName];
    }

    public function __construct($configName, $config = []) {
        $this->configName = $configName;
        $this->driver='phpredis';
        if (!is_array($config)) {
            $config = array();
        }
        if (count($config) == 0) {
            $config = CF::config('database.' . $configName);
        }
        $this->config = $config;
    }

    /**
     * Get a Redis connection by name.
     *
     * @param  string|null  $name
     * @return CRedis_AbstractConnector
     */
    public function connection($name = null) {
        $name = $name ?: 'default';
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }
        return $this->connections[$name] = $this->configure(
                $this->resolve($name), $name
        );
    }

    /**
     * Resolve the given connection by name.
     *
     * @param  string|null  $name
     * @return CRedis_AbstractConnector
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name = null) {
        $name = $name ?: 'default';
        $options = isset($this->config['options']) ? $this->config['options'] : [];
        
        
        if (isset($this->config[$name])) {
            return $this->connector()->connect(
                            $this->parseConnectionConfiguration($this->config[$name]), $options
            );
        }
        if (isset($this->config['clusters'][$name])) {
            return $this->resolveCluster($name);
        }
        throw new InvalidArgumentException("Redis connection [{$name}] not configured.");
    }

    /**
     * Resolve the given cluster connection by name.
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    protected function resolveCluster($name) {
        return $this->connector()->connectToCluster(
                        array_map(function ($config) {
                            return $this->parseConnectionConfiguration($config);
                        }, $this->config['clusters'][$name]), isset($this->config['clusters']) && isset($this->config['clusters']['options']) ? $this->config['clusters']['options'] : [], isset($this->config['options']) ? $this->config['options'] : []
        );
    }

    /**
     * Configure the given connection to prepare it for commands.
     *
     * @param  CRedis_AbstractConnection  $connection
     * @param  string  $name
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
     * @return \Illuminate\Contracts\Redis\Connector
     */
    protected function connector() {
        $customCreator = isset($this->customCreators[$this->driver]) ? $this->customCreators[$this->driver] : null;
        if ($customCreator) {
            return $customCreator();
        }
        switch ($this->driver) {
            case 'predis':
                return new CRedis_Connector_PredisConnector;
            case 'phpredis':
                return new CRedis_Connector_PhpRedisConnector;
        }
    }

    /**
     * Parse the Redis connection configuration.
     *
     * @param  mixed  $config
     * @return array
     */
    protected function parseConnectionConfiguration($config) {
        $parsed = (new CDatabase_ConfigurationUrlParser)->parseConfiguration($config);
        return array_filter($parsed, function ($key) {
            return !in_array($key, ['driver', 'username'], true);
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
     * @param  string  $driver
     * @return void
     */
    public function setDriver($driver) {
        $this->driver = $driver;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback) {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);
        return $this;
    }

    /**
     * Pass methods onto the default Redis connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->connection()->{$method}(...$parameters);
    }

}
