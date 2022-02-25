<?php

/**
 * @internal
 */
final class CVendor_Firebase_Value_Email implements \JsonSerializable {
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value) {
        if (!\filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('The email address is invalid.');
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
