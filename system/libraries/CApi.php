<?php

/**
 * Description of CApi.
 *
 * @author Hery
 */
class CApi {
    const SESSION_DRIVER_FILE = 'File';

    const SESSION_DRIVER_REDIS = 'Redis';

    const SESSION_DRIVER_NULL = 'Null';

    protected static $request;

    /**
     * Get CApi_Runner instance.
     *
     * @return CApi_Runner
     */
    public static function runner() {
        return CApi_Runner::instance();
    }

    public static function createSession($options = []) {
        return CApi_SessionFactory::createSession($options);
    }

    public static function session($sessionId, $options = []) {
        return CApi_SessionFactory::getSession($sessionId, $options);
    }

    public static function setRequest(CApi_HTTP_Request $request) {
        static::$request = $request;
    }

    /**
     * @return CApi_HTTP_Request
     */
    public static function request() {
        return static::$request;
    }

    public static function manager($group = null) {
        return CApi_Manager::instance($group);
    }
}
