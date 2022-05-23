<?php

final class CVendor_Firebase_Request_CreateUser implements CVendor_Firebase_RequestInterface {
    use CVendor_Firebase_Request_Trait_EditUserTrait;

    private function __construct() {
    }

    /**
     * @return self
     */
    public static function new() {
        return new self();
    }

    /**
     * @param array<string, mixed> $properties
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException when invalid properties have been provided
     *
     * @return self
     */
    public static function withProperties(array $properties) {
        return self::withEditableProperties(new self(), $properties);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() {
        return $this->prepareJsonSerialize();
    }
}
