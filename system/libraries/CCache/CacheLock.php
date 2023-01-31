<?php

class CCache_CacheLock extends CCache_LockAbstract {
    /**
     * The cache store implementation.
     *
     * @var \CCache_DriverAbstract
     */
    protected $driver;

    /**
     * Create a new lock instance.
     *
     * @param \CCache_DriverAbstract $driver
     * @param string                 $name
     * @param int                    $seconds
     * @param null|string            $owner
     *
     * @return void
     */
    public function __construct($driver, $name, $seconds, $owner = null) {
        parent::__construct($name, $seconds, $owner);

        $this->driver = $driver;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire() {
        if (method_exists($this->driver, 'add') && $this->seconds > 0) {
            return $this->driver->add(
                $this->name,
                $this->owner,
                $this->seconds
            );
        }

        if (!is_null($this->driver->get($this->name))) {
            return false;
        }

        return ($this->seconds > 0)
                ? $this->driver->put($this->name, $this->owner, $this->seconds)
                : $this->driver->forever($this->name, $this->owner);
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release() {
        if ($this->isOwnedByCurrentProcess()) {
            return $this->driver->forget($this->name);
        }

        return false;
    }

    /**
     * Releases this lock regardless of ownership.
     *
     * @return void
     */
    public function forceRelease() {
        $this->driver->forget($this->name);
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return mixed
     */
    protected function getCurrentOwner() {
        return $this->driver->get($this->name);
    }
}
