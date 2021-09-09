<?php

final class CServer_Domain {
    private static $instance;

    private static $whois;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function whois() {
        return CServer_Domain_WhoIs::instance();
    }
}
