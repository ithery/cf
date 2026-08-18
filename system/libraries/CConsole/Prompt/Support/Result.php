<?php

/**
 * This is a 'sentinel' value. It wraps a return value, which can
 * allow us to differentiate between a `null` return value and
 * a `null` return value that's intended to continue a loop.
 */
final class CConsole_Prompt_Support_Result {
    /**
     * @var mixed
     */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public static function from($value) {
        return new self($value);
    }
}
