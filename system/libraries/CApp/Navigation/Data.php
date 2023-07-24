<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Navigation_Data {
    protected static $navigationCallback = [];

    /**
     * @param string $domain
     *
     * @return array
     */
    public static function get($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        $navFile = CF::getFile('config', 'nav', $domain);
        $data = null;
        if ($navFile != null) {
            $data = include $navFile;
        }
        if ($data == null) {
            $data = CApp::instance()->getNav();
        }

        if (isset(self::$navigationCallback[$domain]) && self::$navigationCallback[$domain] != null && is_callable(self::$navigationCallback[$domain])) {
            $data = CFunction::factory(self::$navigationCallback[$domain])->addArg($data)->execute();
        }

        return $data;
    }

    /**
     * @param callable $navigationCallback
     * @param string   $domain             optional
     */
    public static function setNavigationCallback(callable $navigationCallback, $domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        self::$navigationCallback[$domain] = $navigationCallback;
    }

    /**
     * @param string $domain optional
     */
    public static function removeNavigationCallback($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        self::$navigationCallback[$domain] = null;
    }

    /**
     * @param string $domain optional
     */
    public static function getNavigationCallback($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        return self::$navigationCallback[$domain];
    }
}
