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
            $this->serializedAndCompressedOwner()
        );
    }

    /**
     * Get the owner key, serialized and compressed.
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    protected function serializedAndCompressedOwner(): string {
        $client = $this->redis->client();

        $owner = $client->_serialize($this->owner);

        // https://github.com/phpredis/phpredis/issues/1938
        if ($this->compressed()) {
            if ($this->lzfCompressed()) {
                $owner = \lzf_compress($owner);
            } elseif ($this->zstdCompressed()) {
                $owner = \zstd_compress($owner, $client->getOption(Redis::OPT_COMPRESSION_LEVEL));
            } elseif ($this->lz4Compressed()) {
                $owner = \lz4_compress($owner, $client->getOption(Redis::OPT_COMPRESSION_LEVEL));
            } else {
                throw new UnexpectedValueException(sprintf(
                    'Unknown phpredis compression in use [%d]. Unable to release lock.',
                    $client->getOption(Redis::OPT_COMPRESSION)
                ));
            }
        }

        return $owner;
    }

    /**
     * Determine if compression is enabled.
     *
     * @return bool
     */
    protected function compressed(): bool {
        return $this->redis->client()->getOption(Redis::OPT_COMPRESSION) !== Redis::COMPRESSION_NONE;
    }

    /**
     * Determine if LZF compression is enabled.
     *
     * @return bool
     */
    protected function lzfCompressed() {
        return defined('Redis::COMPRESSION_LZF')
               && $this->redis->client()->getOption(Redis::OPT_COMPRESSION) === Redis::COMPRESSION_LZF;
    }

    /**
     * Determine if ZSTD compression is enabled.
     *
     * @return bool
     */
    protected function zstdCompressed() {
        return defined('Redis::COMPRESSION_ZSTD')
               && $this->redis->client()->getOption(Redis::OPT_COMPRESSION) === Redis::COMPRESSION_ZSTD;
    }

    /**
     * Determine if LZ4 compression is enabled.
     *
     * @return bool
     */
    protected function lz4Compressed() {
        return defined('Redis::COMPRESSION_LZ4')
               && $this->redis->client()->getOption(Redis::OPT_COMPRESSION) === Redis::COMPRESSION_LZ4;
    }
}
