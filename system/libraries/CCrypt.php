<?php

if (!is_callable('random_bytes')) {
    require_once DOCROOT . 'system/vendor/random_compat/random.php';
}

final class CCrypt {
    protected static $encrypter;

    /**
     * @return CCrypt_Encrypter
     */
    public static function encrypter() {
        if (static::$encrypter == null) {
            $key = CF::config('app.key');
            $cipher = CF::config('app.cipher');
            static::$encrypter = new CCrypt_Encrypter(static::parseKey($key), $cipher);
        }

        return static::$encrypter;
    }

    /**
     * Parse the encryption key.
     *
     * @param string $key
     *
     * @return string
     */
    protected static function parseKey($key) {
        if (cstr::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(cstr::after($key, $prefix));
        }

        return $key;
    }

    /**
     * @param null|string $driver
     *
     * @return CCrypt_HashManager
     */
    public static function hasher($driver = null) {
        return CCrypt_HashManager::instance($driver);
    }
}
