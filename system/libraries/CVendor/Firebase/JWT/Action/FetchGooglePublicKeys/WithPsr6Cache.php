<?php

use Psr\Clock\ClockInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @internal
 */
final class CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithPsr6Cache implements CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_HandlerInterface {
    /**
     * @var CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_HandlerInterface
     */
    private $handler;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var ClockInterface
     */
    private $clock;

    public function __construct(CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_HandlerInterface $handler, CacheItemPoolInterface $cache, ClockInterface $clock) {
        $this->handler = $handler;
        $this->cache = $cache;
        $this->clock = $clock;
    }

    /**
     * Undocumented function.
     *
     * @param CVendor_Firebase_JWT_Action_FetchGooglePublicKeys $action
     *
     * @return CVendor_Firebase_JWT_Contract_KeysInterface
     */
    public function handle(CVendor_Firebase_JWT_Action_FetchGooglePublicKeys $action) {
        $now = $this->clock->now();
        $cacheKey = \md5(\get_class($action));

        /** @noinspection PhpUnhandledExceptionInspection */
        $cacheItem = $this->cache->getItem($cacheKey);
        /** @var null|CVendor_Firebase_JWT_Contract_KeysInterface|CVendor_Firebase_JWT_Contract_ExpirableInterface $keys */
        $keys = $cacheItem->get();

        // We deliberately don't care if the cache item is expired here, as long as the keys
        // themselves are not expired
        if ($keys instanceof CVendor_Firebase_JWT_Contract_KeysInterface
            && $keys instanceof CVendor_Firebase_JWT_Contract_ExpirableInterface
            && !$keys->isExpiredAt($now)
        ) {
            return $keys;
        }

        // Non-expiring keys coming from a cache hit can be returned as well
        if ($keys instanceof CVendor_Firebase_JWT_Contract_KeysInterface
            && !($keys instanceof CVendor_Firebase_JWT_Contract_ExpirableInterface)
            && $cacheItem->isHit()
        ) {
            return $keys;
        }

        // At this point, we have to re-fetch the keys, because either the cache item is a miss
        // or the value in the cache item is not a Keys object

        // We need fresh keys
        try {
            $keys = $this->handler->handle($action);
        } catch (CVendor_Firebase_JWT_Exception_FetchingGooglePublicKeysFailedException $e) {
            $reason = \sprintf(
                'The inner handler of %s (%s) failed in fetching keys: %s',
                __CLASS__,
                \get_class($this->handler),
                $e->getMessage()
            );

            throw CVendor_Firebase_JWT_Exception_FetchingGooglePublicKeysFailedException::because($reason, $e->getCode(), $e);
        }

        $cacheItem->set($keys);

        if ($keys instanceof CVendor_Firebase_JWT_Contract_ExpirableInterface) {
            $cacheItem->expiresAt($keys->expiresAt());
        } else {
            $cacheItem->expiresAfter($action->getFallbackCacheDuration()->value());
        }

        $this->cache->save($cacheItem);

        return $keys;
    }
}
