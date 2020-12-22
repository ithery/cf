<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
class CCache_Repository implements ArrayAccess {
    use CTrait_Helper_InteractsWithTime;

    /**
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
     * The event dispatcher implementation.
     *
     * @var CEvent_Dispatcher
     */
    protected $events;

    /**
     * Create a new cache repository instance.
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct($options = []) {
        if ($options instanceof CCache_DriverAbstract) {
            $this->driver = $options;
        } else {
            $driverName = carr::get($options, 'driver', 'Null');
            $driverOption = carr::get($options, 'options', []);

            $this->driver = $this->resolveDriver($driverName, $driverOption);
        }
    }

    public function resolveDriver($driverName, $options = []) {
        switch ($driverName) {
            case 'Redis':
                $redis = CRedis::instance(carr::get($options, 'group', 'redis'));
                return new CCache_Driver_RedisDriver($redis, carr::get($options, 'prefix', ''), carr::get($options, 'connection', 'default'));
                break;
            default:
                $driverClass = 'CCache_Driver_' . $driverName . 'Driver';
                return new $driverClass($options);
        }
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key) {
        return !is_null($this->get($key));
    }

    /**
     * Determine if an item doesn't exist in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function missing($key) {
        return !$this->has($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null) {
        if (is_array($key)) {
            return $this->many($key);
        }
        $value = $this->driver->get($this->itemKey($key));
        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        if (is_null($value)) {
            $this->event(new CCache_Event_CacheMissed($key));

            $value = c::value($default);
        } else {
            $this->event(new CCache_Event_CacheHit($key, $value));
        }

        return $value;
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param array $keys
     *
     * @return array
     */
    public function many(array $keys) {
        $values = $this->driver->many(c::collect($keys)->map(function ($value, $key) {
            return is_string($key) ? $key : $value;
        })->values()->all());

        return c::collect($values)->map(function ($value, $key) use ($keys) {
            return $this->handleManyResult($keys, $key, $value);
        })->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null) {
        $defaults = [];

        foreach ($keys as $key) {
            $defaults[$key] = $default;
        }

        return $this->many($defaults);
    }

    /**
     * Handle a result for the "many" method.
     *
     * @param array  $keys
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function handleManyResult($keys, $key, $value) {
        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        if (is_null($value)) {
            $this->event(new CCache_Event_CacheMissed($key));

            return isset($keys[$key]) ? c::value($keys[$key]) : null;
        }

        // If we found a valid value we will fire the "hit" event and return the value
        // back from this function. The "hit" event gives developers an opportunity
        // to listen for every possible cache "hit" throughout this applications.
        $this->event(new CCache_Event_CacheHit($key, $value));

        return $value;
    }

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function pull($key, $default = null) {
        return c::tap($this->get($key, $default), function () use ($key) {
            $this->forget($key);
        });
    }

    /**
     * Store an item in the cache.
     *
     * @param string                                    $key
     * @param mixed                                     $value
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     *
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
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param array                                     $values
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     *
     * @return bool
     */
    public function putMany(array $values, $ttl = null) {
        if ($ttl === null) {
            return $this->putManyForever($values);
        }

        $seconds = $this->getSeconds($ttl);

        if ($seconds <= 0) {
            return $this->deleteMultiple(array_keys($values));
        }

        $result = $this->driver->putMany($values, $seconds);

        if ($result) {
            foreach ($values as $key => $value) {
                $this->event(new CCache_Event_KeyWritten($key, $value, $seconds));
            }
        }

        return $result;
    }

    /**
     * Store multiple items in the cache indefinitely.
     *
     * @param array $values
     *
     * @return bool
     */
    protected function putManyForever(array $values) {
        $result = true;

        foreach ($values as $key => $value) {
            if (!$this->forever($key, $value)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null) {
        return $this->putMany(is_array($values) ? $values : iterator_to_array($values), $ttl);
    }

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param string                                    $key
     * @param mixed                                     $value
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     *
     * @return bool
     */
    public function add($key, $value, $ttl = null) {
        if ($ttl !== null) {
            if ($this->getSeconds($ttl) <= 0) {
                return false;
            }

            // If the store has an "add" method we will call the method on the store so it
            // has a chance to override this logic. Some drivers better support the way
            // this operation should work with a total "atomic" implementation of it.
            if (method_exists($this->driver, 'add')) {
                $seconds = $this->getSeconds($ttl);

                return $this->driver->add(
                    $this->itemKey($key),
                    $value,
                    $seconds
                );
            }
        }

        // If the value did not exist in the cache, we will put the value in the cache
        // so it exists for subsequent requests. Then, we will return true so it is
        // easy to know if the value gets added. Otherwise, we will return false.
        if (is_null($this->get($key))) {
            return $this->put($key, $value, $ttl);
        }

        return false;
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return int|bool
     */
    public function increment($key, $value = 1) {
        return $this->driver->increment($key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return int|bool
     */
    public function decrement($key, $value = 1) {
        return $this->driver->decrement($key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function forever($key, $value) {
        $result = $this->driver->forever($this->itemKey($key), $value);

        if ($result) {
            $this->event(new CCache_Event_KeyWritten($key, $value));
        }

        return $result;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param string                                    $key
     * @param \DateTimeInterface|\DateInterval|int|null $ttl
     * @param \Closure                                  $callback
     *
     * @return mixed
     */
    public function remember($key, $ttl, Closure $callback) {
        $value = $this->get($key);

        // If the item exists in the cache we will just return this immediately and if
        // not we will execute the given Closure and cache the result of that for a
        // given number of seconds so it's available for all subsequent requests.
        if (!is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $ttl);

        return $value;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @param string   $key
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function sear($key, Closure $callback) {
        return $this->rememberForever($key, $callback);
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @param string   $key
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function rememberForever($key, Closure $callback) {
        $value = $this->get($key);

        // If the item exists in the cache we will just return this immediately
        // and if not we will execute the given Closure and cache the result
        // of that forever so it is available for all subsequent requests.
        if (!is_null($value)) {
            return $value;
        }

        $this->forever($key, $value = $callback());

        return $value;
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function forget($key) {
        return c::tap($this->driver->forget($this->itemKey($key)), function ($result) use ($key) {
            if ($result) {
                $this->event(new CCache_Event_KeyForgotten($key));
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
    public function deleteMultiple($keys) {
        $result = true;

        foreach ($keys as $key) {
            if (!$this->forget($key)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function clear() {
        return $this->driver->flush();
    }

    /**
     * Begin executing a new tags operation if the store supports it.
     *
     * @param array|mixed $names
     *
     * @return CCache_TaggedCache
     *
     * @throws \BadMethodCallException
     */
    public function tags($names) {
        if (!$this->supportsTags()) {
            throw new BadMethodCallException('This cache store does not support tagging.');
        }

        $cache = $this->driver->tags(is_array($names) ? $names : func_get_args());

        if (!is_null($this->events)) {
            $cache->setEventDispatcher($this->events);
        }

        return $cache->setDefaultCacheTime($this->default);
    }

    /**
     * Format the key for a cache item.
     *
     * @param string $key
     *
     * @return string
     */
    protected function itemKey($key) {
        return $key;
    }

    /**
     * Determine if the current store supports tags.
     *
     * @return bool
     */
    public function supportsTags() {
        return method_exists($this->driver, 'tags');
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
     * @param int|null $seconds
     *
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
     * Fire an event for this cache instance.
     *
     * @param string $event
     *
     * @return void
     */
    protected function event($event) {
        if (isset($this->events)) {
            $this->events->dispatch($event);
        }
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return CEvent_Dispatcher
     */
    public function getEventDispatcher() {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param CEvent_Dispatcher $events
     *
     * @return void
     */
    public function setEventDispatcher(CEvent_Dispatcher $events) {
        $this->events = $events;
    }

    /**
     * Determine if a cached value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key) {
        return $this->has($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key) {
        return $this->get($key);
    }

    /**
     * Store an item in the cache for the default time.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value) {
        $this->put($key, $value, $this->default);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     *
     * @return void
     */
    public function offsetUnset($key) {
        $this->forget($key);
    }

    /**
     * Calculate the number of seconds for the given TTL.
     *
     * @param \DateTimeInterface|\DateInterval|int $ttl
     *
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
}
