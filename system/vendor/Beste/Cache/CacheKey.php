<?php

namespace Beste\Cache;

/**
 * @internal
 */
final class CacheKey {
    private string $value;

    private function __construct(string $value) {
        $this->value = $value;
    }

    public static function fromString(string $value): self {
        if (preg_match('/^[a-zA-Z0-9_.-]+$/u', $value) !== 1) {
            throw InvalidArgument::invalidKey();
        }

        return new self($value);
    }

    public function toString(): string {
        return $this->value;
    }
}
