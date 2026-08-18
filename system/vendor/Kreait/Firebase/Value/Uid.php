<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Stringable;

use function mb_strlen;

/**
 * @internal
 */
final class Uid
{
    /**
     * @var non-empty-string
     */
    public string $value;

    private function __construct(string $value)
    {
        if ($value === '' || mb_strlen($value) > 128) {
            throw new InvalidArgumentException('A uid must be a non-empty string with at most 128 characters.');
        }

        $this->value = $value;
    }

    /**
     * Creates a Uid instance from the given string.
     *
     * @param Stringable|string $value
     *
     * @throws InvalidArgumentException if the string is empty or has more than 128 characters
     */
    public static function fromString($value): self
    {
        return new self((string) $value);
    }
}
