<?php

defined('SYSPATH') or die('No direct access allowed.');

class CEnv {
    /**
     * Indicates if the putenv adapter is enabled.
     *
     * @var bool
     */
    protected static $adapter;

    /**
     * Indicates if the putenv adapter is enabled.
     *
     * @var bool
     */
    protected static $dotEnvAdapter;

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

    public static function dotEnvAdapter() {
        if (static::$dotEnvAdapter == null) {
            static::$dotEnvAdapter = new CEnv_Adapter_DotEnvAdapter();
        }

        return static::$dotEnvAdapter;
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
        return static::adapter()->get($key, static::dotEnvAdapter()->get($key, $default));
    }
}
