<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!is_callable('random_bytes')) {
    require_once DOCROOT . 'system/vendor/random_compat/random.php';
}

final class CCrypt {
    protected static $encrypter;

    public static function encrypter() {
        if (static::$encrypter == null) {
            $config = CConfig::instance('app');
            $key = $config->get('key');
            $cipher = $config->get('cipher');

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
}
