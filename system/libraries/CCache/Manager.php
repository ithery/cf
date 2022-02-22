<?php
use Aws\DynamoDb\DynamoDbClient;

class CCache_Manager implements CCache_Contract_FactoryInterface {
    /**
     * The array of resolved cache stores.
     *
     * @var array
     */
    protected $stores = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    private static $instance;

    /**
     * @return CCache_Manager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct() {
    }

    /**
     * Get a cache store instance by name, wrapped in a repository.
     *
     * @param null|string $name
     *
     * @return \CCache_Repository
     */
    public function store($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        return $this->stores[$name] = $this->get($name);
    }

    /**
     * Get a cache driver instance.
     *
     * @param null|string $driver
     *
     * @return \CCache_RepositoryInterface
     */
    public function driver($driver = null) {
        return $this->store($driver);
    }

    /**
     * Attempt to get the store from the local cache.
     *
     * @param string $name
     *
     * @return \CCache_RepositoryInterface
     */
    protected function get($name) {
        if (isset($this->stores[$name])) {
            return $this->stores[$name];
        }

        return $this->resolve($name);
    }

    /**
     * Resolve the given store.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \CCache_RepositoryInterface
     */
    protected function resolve($name) {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

            if (method_exists($this, $driverMethod)) {
                return $this->{$driverMethod}($config);
            } else {
                throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
            }
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param array $config
     *
     * @return mixed
     */
    protected function callCustomCreator(array $config) {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create an instance of the APC cache driver.
     *
     * @param array $config
     *
     * @return \CCache_Repository
     */
    protected function createApcDriver(array $config) {
        $prefix = $this->getPrefix($config);

        return $this->repository(new CCache_Driver_ApcDriver(['prefix' => $prefix]));
    }

    /**
     * Create an instance of the array cache driver.
     *
     * @param array $config
     *
     * @return \CCache_Repository
     */
    protected function createArrayDriver(array $config) {
        return $this->repository(new CCache_Driver_ArrayDriver($config));
    }

    /**
     * Create an instance of the file cache driver.
     *
     * @param array $config
     *
     * @return \CCache_Repository
     */
    protected function createFileDriver(array $config) {
        return $this->repository(new CCache_Driver_FileDriver($config));
    }

    /**
     * Create an instance of the Memcached cache driver.
     *
     * @param array $config
     *
     * @return \CCache_Repository
     */
    protected function createMemcachedDriver(array $config) {
        $prefix = $this->getPrefix($config);

        $memcached = $this->app['memcached.connector']->connect(
            $config['servers'],
            isset($config['persistent_id']) ? $config['persistent_id'] : null,
            isset($config['options']) ? $config['options'] : [],
            array_filter(isset($config['sasl']) ? $config['sasl'] : [])
        );

        return $this->repository(new CCache_Driver_MemcachedDriver($memcached, $prefix));
    }

    /**
     * Create an instance of the Null cache driver.
     *
     * @return \CCache_Repository
     */
    protected function createNullDriver() {
        return $this->repository(new CCache_Driver_NullDriver([]));
    }

    /**
     * Create an instance of the Redis cache driver.
     *
     * @param array $config
     *
     * @return \CCache_Repository
     */
    protected function createRedisDriver(array $config) {
        $redis = CRedis::instance();

        $connection = isset($config['connection']) ? $config['connection'] : 'default';

        $store = new CCache_Driver_RedisDriver($redis, $this->getPrefix($config), $connection);

        return $this->repository(
            $store->setLockConnection(isset($config['lock_connection']) ? $config['lock_connection'] : $connection)
        );
    }

    /**
     * Create an instance of the database cache driver.
     *
     * @param array $config
     *
     * @return \CCache_Repository
     */
    protected function createDatabaseDriver(array $config) {
        $connection = c::db(isset($config['connection']) ? $config['connection'] : null);

        $store = new CCache_Driver_DatabaseDriver(
            $connection,
            $config['table'],
            $this->getPrefix($config),
            isset($config['lock_table']) ? $config['lock_table'] : 'cache_locks',
            isset($config['lock_lottery']) ? $config['lock_lottery'] : [2, 100]
        );

        return $this->repository($store->setLockConnection(
            c::db(isset($config['lock_connection']) ? $config['lock_connection'] : (isset($config['connection']) ? $config['connection'] : null))
        ));
    }

    /**
     * Create an instance of the DynamoDB cache driver.
     *
     * @param array $config
     *
     * @return \CCache_Repository
     */
    protected function createDynamodbDriver(array $config) {
        $client = $this->newDynamodbClient($config);

        return $this->repository(
            new CCache_Driver_DynamoDbDriver(
                $client,
                $config['table'],
                isset($config['attributes']['key']) ? $config['attributes']['key'] : 'key',
                isset($config['attributes']['value']) ? $config['attributes']['value'] : 'value',
                isset($config['attributes']['expiration']) ? $config['attributes']['expiration'] : 'expires_at',
                $this->getPrefix($config)
            )
        );
    }

    /**
     * Create new DynamoDb Client instance.
     *
     * @return DynamoDbClient
     */
    protected function newDynamodbClient(array $config) {
        $dynamoConfig = [
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => isset($config['endpoint']) ? $config['endpoint'] : null,
        ];

        if (isset($config['key'], $config['secret'])) {
            $dynamoConfig['credentials'] = carr::only(
                $config,
                ['key', 'secret', 'token']
            );
        }

        return new DynamoDbClient($dynamoConfig);
    }

    /**
     * Create a new cache repository with the given implementation.
     *
     * @param \CCache_DriverInterface $driver
     *
     * @return \CCache_Repository
     */
    public function repository(CCache_DriverInterface $driver) {
        return new CCache_Repository($driver);
    }

    /**
     * Get the cache prefix.
     *
     * @param array $config
     *
     * @return string
     */
    protected function getPrefix(array $config) {
        return carr::get($config, 'prefix', CF::config('cache.prefix'));
    }

    /**
     * Get the cache connection configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name) {
        if (!is_null($name) && $name !== 'null') {
            return CF::config("cache.stores.{$name}");
        }

        return ['driver' => 'null'];
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return CF::config('cache.default');
    }

    /**
     * Unset the given driver instances.
     *
     * @param null|array|string $name
     *
     * @return $this
     */
    public function forgetDriver($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        foreach ((array) $name as $cacheName) {
            if (isset($this->stores[$cacheName])) {
                unset($this->stores[$cacheName]);
            }
        }

        return $this;
    }

    /**
     * Disconnect the given driver and remove from local cache.
     *
     * @param null|string $name
     *
     * @return void
     */
    public function purge($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        unset($this->stores[$name]);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string   $driver
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend($driver, $callback) {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->store()->$method(...$parameters);
    }
}
