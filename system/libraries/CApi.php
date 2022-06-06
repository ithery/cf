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

    protected static $oauth = [];

    /**
     * @var null|CApi_Dispatcher
     */
    protected static $dispatcher;

    protected static $call = 0;

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

    /**
     * @return null|CApi_Dispatcher
     */
    public static function currentDispatcher() {
        return static::$dispatcher;
    }

    public static function setCurrentDispatcher(CApi_Dispatcher $dispatcher = null) {
        static::$dispatcher = $dispatcher;
    }

    /**
     * @param null|mixed $apiGroup
     *
     * @return null|CApi_OAuth
     */
    public static function oauth($apiGroup = null) {
        static::$call++;

        if (static::$call == 2) {
            cdbg::dd(cdbg::getTraceString());
        }
        if (!is_array(static::$oauth)) {
            static::$oauth = [];
        }
        if ($apiGroup == null) {
            if (static::$dispatcher) {
                $apiGroup = static::$dispatcher->getGroup();
            }
        }
        if ($apiGroup == null) {
            $apiGroup = CF::config('api.default');
        }
        if ($apiGroup != null) {
            if (!isset(static::$oauth[$apiGroup])) {
                static::$oauth[$apiGroup] = new CApi_OAuth($apiGroup);
            }

            return static::$oauth[$apiGroup];
        }

        return null;
    }
}
