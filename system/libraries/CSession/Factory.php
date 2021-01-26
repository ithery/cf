<?php

class CSession_Factory {
    /**
     * @var CSession_Factory
     */
    private static $instance;

    /**
     * @var CConfig
     */
    private $config;

    /**
     * @return CSession_Factory
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct() {
        $this->config = CConfig::instance('session')->all();
    }

    /**
     * Create an instance of the Redis session driver.
     *
     * @return CSession
     */
    public function createRedisDriver() {
        $cacheOptions = [];
        $cacheOptions['driver'] = 'Redis';

        $redis = CRedis::instance(carr::get($this->config, 'storage'));
        $driver = new CCache_Driver_RedisDriver($redis);
        $redisStore = new CCache_Repository($driver);
        $expirationSeconds = carr::get($this->config, 'expiration');
        $handler = new CSession_Driver_Redis($redisStore, $expirationSeconds);
        return $handler;
    }

    /**
     * @return \CSession_Handler_NullSessionHandler
     */
    public function createNullDriver() {
        return new CSession_Handler_NullSessionHandler;
    }

    /**
     * Create an instance of the "array" session driver.
     *
     * @return \CSession_Handler_ArraySessionHandler
     */
    public function createArrayDriver() {
        return new CSession_Handler_ArraySessionHandler(carr::get($this->config, 'expiration'));
    }

    /**
     * Create an instance of the "cookie" session driver.
     *
     * @return \CSession_Handler_CookieSessionHandler
     */
    public function createCookieDriver() {
        return new CSession_Handler_CookieSessionHandler(CHTTP::cookie(), carr::get($this->config, 'expiration'));
    }

    /**
     * Create an instance of the file session driver.
     *
     * @return \CSession_Handler_FileSessionHandler
     */
    public function createFileDriver() {
        return $this->createNativeDriver();
    }

    /**
     * Create an instance of the file session driver.
     *
     * @return \CSession_Handler_FileSessionHandler
     */
    public function createNativeDriver() {
        return new CSession_Handler_FileSessionHandler(carr::get($this->config, 'storage'), carr::get($this->config, 'expiration'));
    }

    /**
     * Create an instance of the database session driver.
     *
     * @return CSession_Handler_DatabaseSessionHandler
     */
    public function createDatabaseDriver() {
        return new CSession_Handler_DatabaseSessionHandler(
            $this->getDatabaseConnection($this->config),
            carr::get($this->config, 'table', 'session'),
            carr::get($this->config, 'expiration')
        );
    }

    /**
     * Get the database connection for the database driver.
     *
     * @return CDatabase
     */
    public function getDatabaseConnection() {
        return CDatabase::instance(carr::get($this->config, 'storage', 'default'));
    }

    /**
     * Create an instance of a cache driven driver.
     *
     * @param string $driver
     *
     * @return \Illuminate\Session\Store
     */
    public function createCacheBased($driver) {
        return $this->createCacheHandler($driver);
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function createDriver($driver) {
        // First, we will determine if a custom driver creator exists for the given driver and
        // if it does not we will check for a creator method for the driver. Custom creator
        // callbacks allow developers to build their own "drivers" easily using Closures.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } else {
            $method = 'create' . cstr::studly($driver) . 'Driver';

            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }
}
