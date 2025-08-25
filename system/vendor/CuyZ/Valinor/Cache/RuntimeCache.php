<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Cache;

/**
 * @internal
 *
 * @template EntryType
 * @implements Cache<EntryType>
 */
final class RuntimeCache implements Cache
{
    /** @var array<string, EntryType|null> */
    private array $entries = [];

    private Cache $delegate;
    public function __construct(
        /** @var Cache<EntryType> */
        Cache $delegate
    ) {
        $this->delegate = $delegate;
    }

    public function get(string $key, ...$arguments)
    {
        return $this->entries[$key] ??= $this->delegate->get($key, ...$arguments);
    }

    public function set(string $key, CacheEntry $entry): void
    {
        $this->delegate->set($key, $entry);
    }

    public function clear(): void
    {
        $this->entries = [];

        $this->delegate->clear();
    }
}
