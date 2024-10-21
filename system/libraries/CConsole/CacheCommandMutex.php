<?php

use Carbon\CarbonInterval;

class CConsole_CacheCommandMutex implements CConsole_CommandMutexInterface {
    use CTrait_Helper_InteractsWithTime;

    /**
     * The cache factory implementation.
     *
     * @var CCache_Contract_FactoryInterface
     */
    public $cache;

    /**
     * The cache store that should be used.
     *
     * @var null|string
     */
    public $store = null;

    /**
     * Create a new command mutex.
     *
     * @param \CCache_Contract_FactoryInterface $cache
     */
    public function __construct(CCache_Contract_FactoryInterface $cache) {
        $this->cache = $cache;
    }

    /**
     * Attempt to obtain a command mutex for the given command.
     *
     * @param \CConsole_Command $command
     *
     * @return bool
     */
    public function create($command) {
        $store = $this->cache->store($this->store);

        $expiresAt = method_exists($command, 'isolationLockExpiresAt')
            ? $command->isolationLockExpiresAt()
            : CarbonInterval::hour();

        if ($this->shouldUseLocks($store->getStore())) {
            return $store->getStore()->lock(
                $this->commandMutexName($command),
                $this->secondsUntil($expiresAt)
            )->get();
        }

        return $store->add($this->commandMutexName($command), true, $expiresAt);
    }

    /**
     * Determine if a command mutex exists for the given command.
     *
     * @param \CConsole_Command $command
     *
     * @return bool
     */
    public function exists($command) {
        $store = $this->cache->store($this->store);

        if ($this->shouldUseLocks($store->getStore())) {
            $lock = $store->getStore()->lock($this->commandMutexName($command));

            return c::tap(!$lock->get(), function ($exists) use ($lock) {
                if ($exists) {
                    $lock->release();
                }
            });
        }

        return $this->cache->store($this->store)->has($this->commandMutexName($command));
    }

    /**
     * Release the mutex for the given command.
     *
     * @param \CConsole_Command $command
     *
     * @return bool
     */
    public function forget($command) {
        $store = $this->cache->store($this->store);

        if ($this->shouldUseLocks($store->getStore())) {
            return $store->getStore()->lock($this->commandMutexName($command))->forceRelease();
        }

        return $this->cache->store($this->store)->forget($this->commandMutexName($command));
    }

    /**
     * Get the isolatable command mutex name.
     *
     * @param \CConsole_Command $command
     *
     * @return string
     */
    protected function commandMutexName($command) {
        $baseName = 'framework' . DIRECTORY_SEPARATOR . 'command-' . $command->getName();

        return method_exists($command, 'isolatableId')
            ? $baseName . '-' . $command->isolatableId()
            : $baseName;
    }

    /**
     * Specify the cache store that should be used.
     *
     * @param null|string $store
     *
     * @return $this
     */
    public function useStore($store) {
        $this->store = $store;

        return $this;
    }

    /**
     * Determine if the given store should use locks for command mutexes.
     *
     * @param \CCache_DriverInterface $store
     *
     * @return bool
     */
    protected function shouldUseLocks($store) {
        return $store instanceof CCache_Contract_LockProviderDriverInterface && !$store instanceof CCache_Driver_DynamoDbDriver;
    }
}
