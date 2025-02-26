<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * PSR-6 cache implementation that connects to Laravel's cache Repository.
 * adapted from https://github.com/madewithlove/illuminate-psr-cache-bridge.
 */
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CCache_PsrBridge_CacheItemPool implements CacheItemPoolInterface {
    /**
     * @var CCache_Repository
     */
    private $repository;

    /**
     * @var \Psr\Cache\CacheItemInterface[]
     */
    private $deferred = [];

    /**
     * @param CCache_RepositoryInterface $repository
     */
    public function __construct(CCache_RepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * Destructor.
     */
    public function __destruct() {
        $this->commit();
    }

    /**
     * @inheritdoc
     */
    public function getItem($key) {
        $this->validateKey($key);
        if (isset($this->deferred[$key])) {
            return clone $this->deferred[$key];
        } elseif ($this->repository->has($key)) {
            return new CCache_PsrBridge_CacheItem($key, unserialize($this->repository->get($key)), true);
        } else {
            return new CCache_PsrBridge_CacheItem($key);
        }
    }

    /**
     * @inheritdoc
     */
    public function getItems(array $keys = []) {
        return array_combine($keys, array_map(function ($key) {
            return $this->getItem($key);
        }, $keys));
    }

    /**
     * @inheritdoc
     */
    public function hasItem($key) {
        $this->validateKey($key);
        if (isset($this->deferred[$key])) {
            $item = $this->deferred[$key];
            $expiresAt = $this->getExpiresAt($item);
            if (!$expiresAt) {
                return true;
            }

            return $expiresAt > new DateTimeImmutable();
        }

        return $this->repository->has($key);
    }

    /**
     * @inheritdoc
     */
    public function clear() {
        try {
            $this->deferred = [];
            $store = $this->repository;
            /** @var CCache_DriverAbstract $store */
            $store->flush();
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteItem($key) {
        $this->validateKey($key);
        unset($this->deferred[$key]);
        if (!$this->hasItem($key)) {
            return true;
        }

        return $this->repository->forget($key);
    }

    /**
     * @inheritdoc
     */
    public function deleteItems(array $keys) {
        // Validating all keys first.
        foreach ($keys as $key) {
            $this->validateKey($key);
        }
        $success = true;
        foreach ($keys as $key) {
            $success = $success && $this->deleteItem($key);
        }

        return $success;
    }

    /**
     * @inheritdoc
     */
    public function save(CacheItemInterface $item) {
        $expiresAt = $this->getExpiresAt($item);
        if (!$expiresAt) {
            try {
                $this->repository->forever($item->getKey(), serialize($item->get()));
            } catch (Exception $exception) {
                return false;
            }

            return true;
        }
        $now = new DateTimeImmutable('now', $expiresAt->getTimezone());
        $seconds = $expiresAt->getTimestamp() - $now->getTimestamp();
        $minutes = (int) floor($seconds / 60.0);
        if ($minutes <= 0) {
            $this->repository->forget($item->getKey());

            return false;
        }

        try {
            $this->repository->put($item->getKey(), serialize($item->get()), $minutes);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function saveDeferred(CacheItemInterface $item) {
        $expiresAt = $this->getExpiresAt($item);
        if ($expiresAt && ($expiresAt < new DateTimeImmutable())) {
            return false;
        }
        $item = (new CCache_PsrBridge_CacheItem($item->getKey(), $item->get(), true))->expiresAt($expiresAt);
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function commit() {
        $success = true;
        foreach ($this->deferred as $key => $item) {
            $success = $success && $this->save($item);
        }
        $this->deferred = [];

        return $success;
    }

    /**
     * @param string $key
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function validateKey($key) {
        if (!is_string($key) || preg_match('#[{}\(\)/\\\\@:]#', $key)) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @param \CCache_PsrBridge_CacheItem $item
     *
     * @return \DateTimeInterface
     */
    private function getExpiresAt(CCache_PsrBridge_CacheItem $item) {
        return $item->getExpiresAt();
    }
}
