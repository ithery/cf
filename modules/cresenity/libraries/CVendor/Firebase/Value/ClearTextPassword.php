<?php

/**
 * @internal
 */
final class CVendor_Firebase_Value_ClearTextPassword implements \JsonSerializable {
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value) {
        if (\mb_strlen($value) < 6) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('A password must be a string with at least 6 characters.');
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function jsonSerialize() {
        return $this->value;
    }

    /**
     * @param self|string $other
     *
     * @return bool
     */
    public function equalsTo($other) {
        return $this->value === (string) $other;
    }
}
