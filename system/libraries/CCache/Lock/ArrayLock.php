<?php

class CCache_Lock_ArrayLock extends CCache_LockAbstract {
    /**
     * The parent array cache store.
     *
     * @var CCache_Driver_ArrayDriver
     */
    protected $driver;

    /**
     * Create a new lock instance.
     *
     * @param CCache_Driver_ArrayDriver $driver
     * @param string                    $name
     * @param int                       $seconds
     * @param null|string               $owner
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
        $expiration = isset($this->driver->locks[$this->name]['expiresAt']) ? $this->driver->locks[$this->name]['expiresAt'] : CCarbon::now()->addSecond();

        if ($this->exists() && $expiration->isFuture()) {
            return false;
        }

        $this->driver->locks[$this->name] = [
            'owner' => $this->owner,
            'expiresAt' => $this->seconds === 0 ? null : CCarbon::now()->addSeconds($this->seconds),
        ];

        return true;
    }

    /**
     * Determine if the current lock exists.
     *
     * @return bool
     */
    protected function exists() {
        return isset($this->driver->locks[$this->name]);
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release() {
        if (!$this->exists()) {
            return false;
        }

        if (!$this->isOwnedByCurrentProcess()) {
            return false;
        }

        $this->forceRelease();

        return true;
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string
     */
    protected function getCurrentOwner() {
        return $this->driver->locks[$this->name]['owner'];
    }

    /**
     * Releases this lock in disregard of ownership.
     *
     * @return void
     */
    public function forceRelease() {
        unset($this->driver->locks[$this->name]);
    }
}
