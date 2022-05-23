<?php

/**
 * @internal
 */
final class CVendor_Firebase_Util {
    /**
     * @param string $name
     *
     * @return null|string
     */
    public static function getenv($name) {
        $value = isset($_SERVER[$name]) ? $_SERVER[$name] : (isset($_ENV[$name]) ? $_ENV[$name] : \getenv($name));

        if ($value !== false && $value !== null) {
            return (string) $value;
        }

        return null;
    }
}
