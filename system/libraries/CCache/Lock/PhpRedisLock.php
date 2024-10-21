<?php

class CCache_Lock_PhpRedisLock extends CCache_Lock_RedisLock {
    /**
     * Create a new phpredis lock instance.
     *
     * @param \CRedis_Connection_PhpRedisConnection $redis
     * @param string                                $name
     * @param int                                   $seconds
     * @param null|string                           $owner
     *
     * @return void
     */
    public function __construct(CRedis_Connection_PhpRedisConnection $redis, string $name, int $seconds, ?string $owner = null) {
        parent::__construct($redis, $name, $seconds, $owner);
    }

    /**
     * @inheritDoc
     */
    public function release() {
        return (bool) $this->redis->eval(
            CCache_LuaScripts::releaseLock(),
            1,
            $this->name,
            ...$this->redis->pack([$this->owner])
        );
    }
}
