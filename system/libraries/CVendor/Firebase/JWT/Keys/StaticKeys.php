<?php

/**
 * @internal
 */
final class CVendor_Firebase_JWT_Keys_StaticKeys implements CVendor_Firebase_JWT_Contract_KeysInterface {
    use CVendor_Firebase_JWT_Concern_KeysTrait;

    private function __construct() {
    }

    /**
     * @return self
     */
    public static function empty() {
        return new self();
    }

    /**
     * @param array<string, string> $values
     *
     * @return self
     */
    public static function withValues(array $values) {
        $keys = new self();
        $keys->values = $values;

        return $keys;
    }
}
