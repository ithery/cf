<?php

/**
 * @see https://github.com/io-developer/php-whois/tree/master/src/Iodev/Whois
 */
class CServer_Domain_Whois {
    protected $tldModule;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
