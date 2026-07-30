<?php

defined('SYSPATH') or die('No direct access allowed.');

class CNavigation_Data {
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

        return static::resolveCallableSubnav($data);
    }

    /**
     * Resolve a `subnav` given as a callable into the array every consumer
     * expects, so a nav definition can be built dynamically (for example from
     * the database) instead of being a static list.
     *
     * The callable receives the nav entry it belongs to, so it can read `name`
     * or any extra key it needs; a closure declaring no parameter works too,
     * since PHP ignores surplus arguments on userland functions.
     *
     * Resolving here — the single point where nav data is loaded — keeps every
     * downstream `is_array($nav['subnav'])` check in CApp_Navigation and
     * CApp_Navigation_Helper working untouched.
     *
     * @param mixed $navs
     *
     * @return mixed
     */
    protected static function resolveCallableSubnav($navs) {
        if (!is_array($navs)) {
            return $navs;
        }

        foreach ($navs as $key => $nav) {
            if (!is_array($nav) || !isset($nav['subnav'])) {
                continue;
            }

            if (!is_array($nav['subnav']) && is_callable($nav['subnav'])) {
                $nav['subnav'] = CFunction::factory($nav['subnav'])->addArg($nav)->execute();
            }

            if (is_array($nav['subnav'])) {
                $nav['subnav'] = static::resolveCallableSubnav($nav['subnav']);
            }

            $navs[$key] = $nav;
        }

        return $navs;
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
