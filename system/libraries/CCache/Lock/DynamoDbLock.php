<?php

class CCache_Lock_DynamoDbLock extends CCache_LockAbstract {
    /**
     * The DynamoDB client instance.
     *
     * @var \CCache_Driver_DynamoDbDriver
     */
    protected $dynamo;

    /**
     * Create a new lock instance.
     *
     * @param \CCache_Driver_DynamoDbDriver $dynamo
     * @param string                        $name
     * @param int                           $seconds
     * @param null|string                   $owner
     *
     * @return void
     */
    public function __construct(CCache_Driver_DynamoDbDriver $dynamo, $name, $seconds, $owner = null) {
        parent::__construct($name, $seconds, $owner);

        $this->dynamo = $dynamo;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire() {
        return $this->dynamo->add(
            $this->name,
            $this->owner,
            $this->seconds
        );
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release() {
        if ($this->isOwnedByCurrentProcess()) {
            return $this->dynamo->forget($this->name);
        }

        return false;
    }

    /**
     * Release this lock in disregard of ownership.
     *
     * @return void
     */
    public function forceRelease() {
        $this->dynamo->forget($this->name);
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return mixed
     */
    protected function getCurrentOwner() {
        return $this->dynamo->get($this->name);
    }
}
