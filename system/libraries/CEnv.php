<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CEnv {
    /**
     * Indicates if the putenv adapter is enabled.
     *
     * @var bool
     */
    protected static $adapter;

    /**
     * The environment repository instance.
     *
     * @var null|\Dotenv\Repository\RepositoryInterface
     */
    protected static $repository;

    /**
     * Enable the putenv adapter.
     *
     * @return void
     */
    public static function enablePutenv() {
        static::adapter()->enablePutenv();
    }

    public static function adapter() {
        if (static::$adapter == null) {
            static::$adapter = static::resolveAdapter();
        }

        return static::$adapter;
    }

    protected static function resolveAdapter() {
        $appPath = c::appRoot();
        if (CFile::exists($appPath . 'env.php')) {
            return new CEnv_Adapter_PhpAdapter();
        }

        return new CEnv_Adapter_NullAdapter();
    }

    /**
     * Disable the putenv adapter.
     *
     * @return void
     */
    public static function disablePutenv() {
        static::adapter()->disablePutenv();
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get($key, $default = null) {
        return static::adapter()->get($key, $default);
    }
}
