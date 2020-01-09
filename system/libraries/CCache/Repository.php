<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:50:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache_Repository implements ArrayAccess {

    use CTrait_Helper_InteractsWithTime;

    /**
     *
     * @var CCache_DriverAbstract 
     */
    protected $driver;

    /**
     * The default number of seconds to store items.
     *
     * @var int|null
     */
    protected $default = 3600;

    /**
     * Create a new cache repository instance.
     *
     * @param  array  $options
     * @return void
     */
    public function __construct($options = array()) {
        if ($options instanceof CCache_DriverAbstract) {
            $this->driver = $options;
        } else {
            $driverName = carr::get($options, 'driver', 'Null');
            $driverOption = carr::get($options, 'options', array());
            $driverClass = 'CCache_Driver_' . $driverName . 'Driver';
            $this->driver = new $driverClass($driverOption);
        }
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key) {
        return !is_null($this->get($key));
    }

    /**
     * Determine if an item doesn't exist in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function missing($key) {
        return !$this->has($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null) {

        $value = $this->driver->get($this->itemKey($key));
        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        if (is_null($value)) {
            $value = CF::value($default);
        }
        return $value;
    }

    /**
     * Store an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function put($key, $value, $ttl = null) {

        if ($ttl === null) {
            return $this->forever($key, $value);
        }
        $seconds = $this->getSeconds($ttl);
        if ($seconds <= 0) {
            return $this->forget($key);
        }
        $result = $this->driver->put($this->itemKey($key), $value, $seconds);

        return $result;
    }

    public function set($key, $value, $ttl = null) {
        return $this->put($key, $value, $ttl);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key) {
        return CF::tap($this->driver->forget($this->itemKey($key)), function ($result) use ($key) {
                    if ($result) {
                        //event fired for success
                    }
                });
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key) {
        return $this->forget($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear() {
        return $this->driver->flush();
    }

    /**
     * Format the key for a cache item.
     *
     * @param  string  $key
     * @return string
     */
    protected function itemKey($key) {
        return $key;
    }

    /**
     * Get the default cache time.
     *
     * @return int
     */
    public function getDefaultCacheTime() {
        return $this->default;
    }

    /**
     * Set the default cache time in seconds.
     *
     * @param  int|null  $seconds
     * @return $this
     */
    public function setDefaultCacheTime($seconds) {
        $this->default = $seconds;
        return $this;
    }

    /**
     * Get the cache driver implementation.
     *
     * @return CCache_Driver
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Determine if a cached value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key) {
        return $this->has($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key) {
        return $this->get($key);
    }

    /**
     * Store an item in the cache for the default time.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value) {
        $this->put($key, $value, $this->default);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key) {
        $this->forget($key);
    }

    /**
     * Calculate the number of seconds for the given TTL.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $ttl
     * @return int
     */
    protected function getSeconds($ttl) {
        $duration = $this->parseDateInterval($ttl);
        if ($duration instanceof DateTimeInterface) {
            $duration = CCarbon::now()->diffInRealSeconds($duration, false);
        }
        return (int) $duration > 0 ? $duration : 0;
    }

    /**
     * Clone cache repository instance.
     *
     * @return void
     */
    public function __clone() {
        $this->driver = clone $this->driver;
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value) {
        $this->driver->forever($this->itemKey($key), $value);

        //$this->event(new KeyWritten($key, $value, 0));
    }

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|int  $minutes
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember($key, $minutes, Closure $callback) {
        $value = $this->get($key);

        // If the item exists in the cache we will just return this immediately and if
        // not we will execute the given Closure and cache the result of that for a
        // given number of minutes so it's available for all subsequent requests.
        if (!is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $minutes);

        return $value;
    }

}
