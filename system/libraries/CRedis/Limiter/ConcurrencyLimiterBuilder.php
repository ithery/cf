<?php

class CRedis_Limiter_ConcurrencyLimiterBuilder {
    use CTrait_Helper_InteractsWithTime;

    /**
     * The Redis connection.
     *
     * @var CRedis_AbstractConnection
     */
    public $connection;

    /**
     * The name of the lock.
     *
     * @var string
     */
    public $name;

    /**
     * The maximum number of entities that can hold the lock at the same time.
     *
     * @var int
     */
    public $maxLocks;

    /**
     * The number of seconds to maintain the lock until it is automatically released.
     *
     * @var int
     */
    public $releaseAfter = 60;

    /**
     * The amount of time to block until a lock is available.
     *
     * @var int
     */
    public $timeout = 3;

    /**
     * Create a new builder instance.
     *
     * @param CRedis_AbstractConnection $connection
     * @param string                    $name
     *
     * @return void
     */
    public function __construct($connection, $name) {
        $this->name = $name;
        $this->connection = $connection;
    }

    /**
     * Set the maximum number of locks that can obtained per time window.
     *
     * @param int $maxLocks
     *
     * @return $this
     */
    public function limit($maxLocks) {
        $this->maxLocks = $maxLocks;
        return $this;
    }

    /**
     * Set the number of seconds until the lock will be released.
     *
     * @param int $releaseAfter
     *
     * @return $this
     */
    public function releaseAfter($releaseAfter) {
        $this->releaseAfter = $this->secondsUntil($releaseAfter);
        return $this;
    }

    /**
     * Set the amount of time to block until a lock is available.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function block($timeout) {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Execute the given callback if a lock is obtained, otherwise call the failure callback.
     *
     * @param callable      $callback
     * @param callable|null $failure
     *
     * @return mixed
     *
     * @throws CRedis_Exception_LimiterTimeoutException
     */
    public function then(callable $callback, callable $failure = null) {
        try {
            return (new CRedis_Limiter_ConcurrencyLimiter(
                $this->connection,
                $this->name,
                $this->maxLocks,
                $this->releaseAfter
            ))->block($this->timeout, $callback);
        } catch (CRedis_Exception_LimiterTimeoutException $e) {
            if ($failure) {
                return $failure($e);
            }
            throw $e;
        }
    }
}
