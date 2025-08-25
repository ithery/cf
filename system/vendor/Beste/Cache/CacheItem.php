<?php

namespace Beste\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Clock\ClockInterface;

/**
 * @internal
 */
final class CacheItem implements CacheItemInterface
{
    /**
     * @var mixed
     */
    private $value;
    private ?\DateTimeInterface $expiresAt;
    private bool $isHit;
    private CacheKey $key;
    private ClockInterface $clock;

    public function __construct(CacheKey $key, ClockInterface $clock)
    {
        $this->key = $key;
        $this->clock = $clock;
        $this->value = null;
        $this->expiresAt = null;
        $this->isHit = false;
    }

    public function getKey(): string
    {
        return $this->key->toString();
    }

    public function get(): mixed
    {
        if ($this->isHit()) {
            return $this->value;
        }

        return null;
    }

    public function isHit(): bool
    {
        if ($this->isHit === false) {
            return false;
        }

        if ($this->expiresAt === null) {
            return true;
        }

        return $this->clock->now()->getTimestamp() < $this->expiresAt->getTimestamp();
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function set($value)
    {
        $this->isHit = true;
        $this->value = $value;

        return $this;
    }

    /**
     * @param \DateTimeInterface|null $expiration
     * @return void
     */
    public function expiresAt($expiration)
    {
        $this->expiresAt = $expiration;

        return $this;
    }

    /**
     * @param \DateInterval|integer|null $time
     * @return static
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->expiresAt = null;
            return $this;
        }

        if (is_int($time)) {
            $time = new \DateInterval("PT{$time}S");
        }

        $this->expiresAt = $this->clock->now()->add($time);

        return $this;
    }
}
