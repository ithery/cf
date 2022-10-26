<?php

class CCron_CacheEventMutex implements CCron_Contract_EventMutexInterface, CCron_Contract_CacheAwareInterface {
    /**
     * The cache store that should be used.
     *
     * @var null|string
     */
    public $store;

    /**
     * Create a new overlapping strategy.
     *
     * @return void
     */
    public function __construct() {
        $this->store = CF::config('cron.cache.store');
    }

    /**
     * Attempt to obtain an event mutex for the given event.
     *
     * @param \CCron_Event $event
     *
     * @return bool
     */
    public function create(CCron_Event $event) {
        return CCache::manager()->store($this->store)->add(
            $event->mutexName(),
            true,
            $event->expiresAt * 60
        );
    }

    /**
     * Determine if an event mutex exists for the given event.
     *
     * @param \CCron_Event $event
     *
     * @return bool
     */
    public function exists(CCron_Event $event) {
        return $this->cache->store($this->store)->has($event->mutexName());
    }

    /**
     * Clear the event mutex for the given event.
     *
     * @param \CCron_Event $event
     *
     * @return void
     */
    public function forget(CCron_Event $event) {
        $this->cache->store($this->store)->forget($event->mutexName());
    }

    /**
     * Specify the cache store that should be used.
     *
     * @param string $store
     *
     * @return $this
     */
    public function useStore($store) {
        $this->store = $store;

        return $this;
    }
}
