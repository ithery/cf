<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache;

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Exception\InvalidArgumentException;
use Symfony\Component\Cache\Exception\LogicException;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class CacheItem implements ItemInterface {
    const METADATA_EXPIRY_OFFSET = 1527506807;

    protected $key;

    protected $value;

    protected $isHit = false;

    protected $expiry;

    protected $defaultLifetime;

    protected $metadata = [];

    protected $newMetadata = [];

    protected $innerItem;

    protected $poolHash;

    protected $isTaggable = false;

    /**
     * {@inheritdoc}
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function get() {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit() {
        return $this->isHit;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function set($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function expiresAt($expiration) {
        if (null === $expiration) {
            $this->expiry = $this->defaultLifetime > 0 ? microtime(true) + $this->defaultLifetime : null;
        } elseif ($expiration instanceof \DateTimeInterface) {
            $this->expiry = (float) $expiration->format('U.u');
        } else {
            throw new InvalidArgumentException(sprintf('Expiration date must implement DateTimeInterface or be null, "%s" given', \is_object($expiration) ? \get_class($expiration) : \gettype($expiration)));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function expiresAfter($time) {
        if (null === $time) {
            $this->expiry = $this->defaultLifetime > 0 ? microtime(true) + $this->defaultLifetime : null;
        } elseif ($time instanceof \DateInterval) {
            $this->expiry = microtime(true) + \DateTime::createFromFormat('U', 0)->add($time)->format('U.u');
        } elseif (\is_int($time)) {
            $this->expiry = $time + microtime(true);
        } else {
            throw new InvalidArgumentException(sprintf('Expiration date must be an integer, a DateInterval or null, "%s" given', \is_object($time) ? \get_class($time) : \gettype($time)));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function tag($tags) {
        if (!$this->isTaggable) {
            throw new LogicException(sprintf('Cache item "%s" comes from a non tag-aware pool: you cannot tag it.', $this->key));
        }
        if (!is_iterable($tags)) {
            $tags = [$tags];
        }
        foreach ($tags as $tag) {
            if (!\is_string($tag)) {
                throw new InvalidArgumentException(sprintf('Cache tag must be string, "%s" given', \is_object($tag) ? \get_class($tag) : \gettype($tag)));
            }
            if (isset($this->newMetadata[self::METADATA_TAGS][$tag])) {
                continue;
            }
            if ('' === $tag) {
                throw new InvalidArgumentException('Cache tag length must be greater than zero');
            }
            if (false !== strpbrk($tag, self::RESERVED_CHARACTERS)) {
                throw new InvalidArgumentException(sprintf('Cache tag "%s" contains reserved characters %s', $tag, self::RESERVED_CHARACTERS));
            }
            $this->newMetadata[self::METADATA_TAGS][$tag] = $tag;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata() {
        return $this->metadata;
    }

    /**
     * Validates a cache key according to PSR-6.
     *
     * @param string $key The key to validate
     *
     * @throws InvalidArgumentException When $key is not valid
     */
    public static function validateKey($key) {
        if (!\is_string($key)) {
            throw new InvalidArgumentException(sprintf('Cache key must be string, "%s" given', \is_object($key) ? \get_class($key) : \gettype($key)));
        }
        if ('' === $key) {
            throw new InvalidArgumentException('Cache key length must be greater than zero');
        }
        if (false !== strpbrk($key, self::RESERVED_CHARACTERS)) {
            throw new InvalidArgumentException(sprintf('Cache key "%s" contains reserved characters %s', $key, self::RESERVED_CHARACTERS));
        }

        return $key;
    }

    /**
     * Internal logging helper.
     *
     * @internal
     *
     * @param mixed $message
     */
    public static function log(LoggerInterface $logger, $message, array $context = []) {
        if ($logger) {
            $logger->warning($message, $context);
        } else {
            $replace = [];
            foreach ($context as $k => $v) {
                if (is_scalar($v)) {
                    $replace['{' . $k . '}'] = $v;
                }
            }
            @trigger_error(strtr($message, $replace), E_USER_WARNING);
        }
    }
}
