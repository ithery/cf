<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CCache {

    /**
     * The cache instance
     * 
     * @var CCache
     */
    protected $instance;
    
    /**
     * The array of resolved cache stores.
     *
     * @var array
     */
    protected $stores = [];

    /**
     * Get a cache store instance by name.
     *
     * @param  string|null  $name
     * @return CCache_Repository
     */
    public function store($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        return self::$stores[$name] = self::get($name);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->store()->$method(...$parameters);
    }

    /**
     * Attempt to get the store from the local cache.
     *
     * @param  string  $name
     * @return CCache_Repository
     */
    protected function get($name) {
        return isset($this->stores[$name]) ? $this->stores[$name] : $this->resolve($name);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args) {
        $instance = static::instance();

        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        call_user_func_array(array($instance, $method), $args);
    }

}
