<?php

use CVendor_Firebase_Messaging_RegistrationToken as RegistrationToken;

final class CVendor_Firebase_Messaging_RegistrationTokens implements Countable, IteratorAggregate {
    /** @var RegistrationToken[] */
    private $tokens;

    public function __construct(RegistrationToken ...$tokens) {
        $this->tokens = $tokens;
    }

    /**
     * @param mixed $values
     *
     * @throws InvalidArgumentException
     */
    public static function fromValue($values) {
        if ($values instanceof self) {
            $tokens = $values->values();
        } elseif ($values instanceof RegistrationToken) {
            $tokens = [$values];
        } elseif (\is_string($values)) {
            $tokens = [RegistrationToken::fromValue($values)];
        } elseif (\is_array($values)) {
            $tokens = [];

            foreach ($values as $value) {
                if ($value instanceof RegistrationToken) {
                    $tokens[] = $value;
                } elseif (\is_string($value)) {
                    $tokens[] = RegistrationToken::fromValue($value);
                }
            }
        } else {
            throw new InvalidArgumentException('Unsupported value(s)');
        }

        return new self(...$tokens);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Generator|RegistrationToken[]
     */
    public function getIterator() {
        return new ArrayIterator($this->tokens);
    }

    public function isEmpty() {
        return \count($this->tokens) === 0;
    }

    /**
     * @return RegistrationToken[]
     */
    public function values() {
        return $this->tokens;
    }

    /**
     * @return string[]
     */
    public function asStrings() {
        return \array_map('strval', $this->tokens);
    }

    public function count() {
        return \count($this->tokens);
    }
}
