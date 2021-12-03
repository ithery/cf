<?php
class CCron_CacheSchedulingMutex implements CCron_Contract_SchedulingMutexInterface, CCron_Contract_CacheAwareInterface {
    /**
     * The cache store that should be used.
     *
     * @var null|string
     */
    public $store;

    /**
     * Create a new scheduling strategy.
     *
     * @return void
     */
    public function __construct() {
        $this->store = CF::config('schedule.cache.store');
    }

    /**
     * Attempt to obtain a scheduling mutex for the given event.
     *
     * @param \CCron_Event $event
     * @param \DateTimeInterface       $time
     *
     * @return bool
     */
    public function create(CCron_Event $event, DateTimeInterface $time) {
        return CCache::manager()->store($this->store)->add(
            $event->mutexName() . $time->format('Hi'),
            true,
            3600
        );
    }

    /**
     * Determine if a scheduling mutex exists for the given event.
     *
     * @param \CCron_Event $event
     * @param \DateTimeInterface       $time
     *
     * @return bool
     */
    public function exists(CCron_Event $event, DateTimeInterface $time) {
        return CCache::manager()->store($this->store)->has(
            $event->mutexName() . $time->format('Hi')
        );
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
