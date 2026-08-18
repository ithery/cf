<?php

defined('SYSPATH') or die('No direct access allowed.');

class CNavigation_Data {
    protected static $navigationCallback = [];

    /**
     * Resolved navigation per domain, for the lifetime of the request.
     *
     * get() is reached from nine places in the framework and several of them run
     * within a single page render (rendering, access filtering, active-item
     * detection). Without this, a callable `subnav` backed by a query would run
     * once per call instead of once per request.
     *
     * @var array
     */
    protected static $resolved = [];

    /**
     * Resolved callable subnav, keyed by nav name, for the lifetime of the request.
     *
     * @var array
     */
    protected static $resolvedSubnav = [];

    /**
     * @param string $domain
     *
     * @return array
     */
    public static function get($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if (array_key_exists($domain, static::$resolved)) {
            return static::$resolved[$domain];
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

        static::$resolved[$domain] = $data;

        return static::$resolved[$domain];
    }

    /**
     * Read a nav entry's `subnav`, resolving it first when it was given as a
     * callable so a nav definition can be built dynamically (for example one
     * entry per row in a table) instead of being a static list.
     *
     * Resolution is lazy on purpose: every place that reads `subnav` goes
     * through here, so the callable of a menu that is never inspected is never
     * executed. The result is memoised per nav name because a single page
     * render reads the same `subnav` from several places (rendering, access
     * filtering, active-item detection).
     *
     * The callable receives the nav entry it belongs to, so it can read `name`
     * or any extra key it needs; a closure declaring no parameter works too,
     * since PHP ignores surplus arguments on userland functions.
     *
     * @param mixed $nav a nav entry, or a `subnav` value directly
     *
     * @return array
     */
    public static function resolveSubnav($nav) {
        $subnav = $nav;
        $key = null;
        if (is_array($nav)) {
            if (!isset($nav['subnav'])) {
                return [];
            }
            $subnav = $nav['subnav'];
            $key = isset($nav['name']) ? $nav['name'] : null;
        }

        if (is_array($subnav)) {
            return $subnav;
        }
        if (!is_callable($subnav)) {
            return [];
        }

        if ($key !== null && array_key_exists($key, static::$resolvedSubnav)) {
            return static::$resolvedSubnav[$key];
        }

        $resolved = CFunction::factory($subnav)->addArg(is_array($nav) ? $nav : [])->execute();
        $resolved = is_array($resolved) ? $resolved : [];

        if ($key !== null) {
            static::$resolvedSubnav[$key] = $resolved;
        }

        return $resolved;
    }

    /**
     * Whether a nav entry has any child, resolving a callable `subnav` when
     * needed.
     *
     * @param mixed $nav
     *
     * @return bool
     */
    public static function hasSubnav($nav) {
        return count(static::resolveSubnav($nav)) > 0;
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
        static::flush($domain);
    }

    /**
     * @param string $domain optional
     */
    public static function removeNavigationCallback($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        self::$navigationCallback[$domain] = null;
        static::flush($domain);
    }

    /**
     * Buang hasil resolusi, misalnya setelah data sumber subnav berubah dalam
     * request yang sama.
     *
     * @param string $domain optional
     *
     * @return void
     */
    public static function flush($domain = null) {
        static::$resolvedSubnav = [];
        if ($domain == null) {
            static::$resolved = [];

            return;
        }
        unset(static::$resolved[$domain]);
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
