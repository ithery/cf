<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:07:08 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache_Driver_FileDriver extends CCache_DriverAbstract {

    use CTrait_Helper_InteractsWithTime;

    protected $engine;

    public function __construct($options) {
        parent::__construct($options);
        $engineName = $this->getOption('engine', 'Array');
        $engineOptions = $this->getOption('options', array());
        $engineClass = 'CCache_Driver_FileDriver_Engine_' . $engineName . 'Engine';
        $this->engine = new $engineClass($engineOptions);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int
     */
    public function decrement($key, $value = 1) {
        return $this->increment($key, $value * -1);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush() {
        if (!$this->engine->isDirectory($this->directory)) {
            return false;
        }
        foreach ($this->engine->directories($this->directory) as $directory) {
            if (!$this->engine->deleteDirectory($directory)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return bool
     */
    public function forever($key, $value) {
        return $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key) {
        if ($this->engine->exists($key)) {
            return $this->engine->delete($key);
        }
        return false;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key) {
        return isset($this->getPayload($key)['data']) ? $this->getPayload($key)['data'] : null;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix() {
        return '';
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int
     */
    public function increment($key, $value = 1) {
        $raw = $this->getPayload($key);
        return CF::tap(((int) $raw['data']) + $value, function ($newValue) use ($key, $raw) {
                    $this->put($key, $newValue, isset($raw['time']) ? $raw['time'] : 0);
                });
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds) {
        $result = $this->engine->put($key, $this->expiration($seconds) . serialize($value), true);
        return $result !== false && $result > 0;
    }

    /**
     * Get a default empty payload for the cache.
     *
     * @return array
     */
    protected function emptyPayload() {
        return ['data' => null, 'time' => null];
    }

    /**
     * Retrieve an item and expiry time from the cache by key.
     *
     * @param  string  $key
     * @return array
     */
    protected function getPayload($key) {

        // If the file doesn't exist, we obviously cannot return the cache so we will
        // just return null. Otherwise, we'll get the contents of the file and get
        // the expiration UNIX timestamps from the start of the file's contents.
        try {
            $expire = substr(
                    $contents = $this->engine->get($key, true), 0, 10
            );
        } catch (Exception $e) {
            return $this->emptyPayload();
        }
        // If the current time is greater than expiration timestamps we will delete
        // the file and return null. This helps clean up the old files and keeps
        // this directory much cleaner for us as old files aren't hanging out.
        if ($this->currentTime() >= $expire) {
            $this->forget($key);
            return $this->emptyPayload();
        }
        $data = unserialize(substr($contents, 10));
        // Next, we'll extract the number of seconds that are remaining for a cache
        // so that we can properly retain the time for things like the increment
        // operation that may be performed on this cache on a later operation.
        $time = $expire - $this->currentTime();
        return compact('data', 'time');
    }

    /**
     * Get the expiration time based on the given seconds.
     *
     * @param  int  $seconds
     * @return int
     */
    protected function expiration($seconds) {
        $time = $this->availableAt($seconds);
        return $seconds === 0 || $time > 9999999999 ? 9999999999 : $time;
    }

}
