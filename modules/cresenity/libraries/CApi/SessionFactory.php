<?php

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since May 30, 2020
 */
final class CApi_SessionFactory {
    private static $instance;

    protected static function newSessionId() {
        $prefix = date('YmdHis');
        $sessionId = uniqid($prefix);

        return $sessionId;
    }

    /**
     * @param array $options
     *
     * @return CApi_SessionAbstract
     */
    public static function createSession($options = []) {
        $driver = carr::get($options, 'driver', CApi::SESSION_DRIVER_FILE);

        $sessionId = static::newSessionId();
        $data = [
            'sessionId' => $sessionId
        ];

        $driver = static::createDriver($driver, $options);
        $driver->write($sessionId, $data);
        return static::getSession($sessionId, $options);
    }

    /**
     * Undocumented function
     *
     * @param string $sessionId
     * @param array  $options
     *
     * @return CApi_Session
     */
    public static function getSession($sessionId, $options = []) {
        $driver = carr::get($options, 'driver', CApi::SESSION_DRIVER_FILE);
        $session = new CApi_Session(static::createDriver($driver, $options), $sessionId);
        return $session;
    }

    /**
     * @param string $driver
     * @param array  $options
     *
     * @return CApi_Session_DriverAbstract
     */
    public static function createDriver($driver, $options = []) {
        $method = 'create' . $driver . 'Driver';
        return static::$method($options);
    }

    public static function createNullDriver($options) {
        return new CApi_Session_Driver_NullDriver($options);
    }

    public static function createFileDriver($options) {
        return new CApi_Session_Driver_FileDriver($options);
    }

    public static function createRedisDriver($options) {
        $cacheOptions = [];
        $cacheOptions['driver'] = 'Redis';

        $storage = 'redis';
        $expirationSeconds = 60 * 60 * 24 * 30;

        $redis = CRedis::instance($storage);
        $driver = new CCache_Driver_RedisDriver($redis);
        $redisStore = new CCache_Repository($driver);
        //$expirationSeconds = CF::config($expiration);
        $handler = new CApi_Session_Driver_RedisDriver($options, $redisStore, $expirationSeconds);
        return $handler;
    }
}
