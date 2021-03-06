<?php

/**
 * Description of CacheBasedSessionHandler
 *
 * @author Hery
 */
class CSession_Handler_CacheBasedSessionHandler implements SessionHandlerInterface {
    /**
     * The cache repository instance.
     *
     * @var CCache_Repository
     */
    protected $cache;

    /**
     * The number of seconds to store the data in the cache.
     *
     * @var int
     */
    protected $seconds;

    /**
     * Create a new cache driven handler instance.
     *
     * @param CCache_Repository $cache
     * @param int               $seconds
     *
     * @return void
     */
    public function __construct(CCache_Repository $cache, $seconds) {
        $this->cache = $cache;
        $this->seconds = $seconds;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName) {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId) {
        return $this->cache->get($sessionId, '');
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data) {
        return $this->cache->put($sessionId, $data, $this->minutes * 60);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId) {
        return $this->cache->forget($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime) {
        return true;
    }

    /**
     * Get the underlying cache repository.
     *
     * @return CCache_Repository
     */
    public function getCache() {
        return $this->cache;
    }
}
