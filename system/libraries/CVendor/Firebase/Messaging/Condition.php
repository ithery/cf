<?php
final class CVendor_Firebase_Messaging_Condition implements \JsonSerializable {
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    private function __construct($value) {
        $this->value = $value;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public static function fromValue($value) {
        $value = \str_replace('"', "'", $value);

        if ((\mb_substr_count($value, "'") % 2) !== 0) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException(\sprintf('The condition "%s" contains an uneven amount of quotes.', $value));
        }

        if (\mb_substr_count(\mb_strtolower($value), 'in topics') > 5) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException(\sprintf('The condition "%s" containts more than 5 topics.', $value));
        }

        return new self($value);
    }

    /**
     * @return string
     */
    public function value() {
        return $this->value;
    }

    public function __toString() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function jsonSerialize() {
        return $this->value;
    }
}
