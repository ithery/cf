<?php

final class CVendor_Firebase_Messaging_Topic implements \JsonSerializable {
    /** @var string */
    private $value;

    private function __construct(string $value) {
        $this->value = $value;
    }

    public static function fromValue(string $value) {
        $value = \trim((string) \preg_replace('@^/topic/@', '', $value), '/');

        if (\preg_match('/[^a-zA-Z0-9-_.~]$/', $value)) {
            throw new CVendor_Firebase_Messaging_Exception_InvalidArgumentException(\sprintf('Malformed topic name "%s".', $value));
        }

        return new self($value);
    }

    public function value() {
        return $this->value;
    }

    public function __toString() {
        return $this->value;
    }

    public function jsonSerialize() {
        return $this->value;
    }
}
