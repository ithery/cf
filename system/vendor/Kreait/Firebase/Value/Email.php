<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Stringable;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

/**
 * @internal
 */
final class Email
{
    /**
     * @var non-empty-string
     */
    public string $value;

    private function __construct(string $value)
    {
        if ($value === '' || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('The email address is invalid.');
        }

        $this->value = $value;
    }

    /**
     * @param Stringable|string $value
     * @return self
     */
    public static function fromString($value): self
    {
        return new self((string) $value);
    }
}
