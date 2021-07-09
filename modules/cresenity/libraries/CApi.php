<?php

/**
 * Description of CApi
 *
 * @author Hery
 */
class CApi {
    const SESSION_DRIVER_FILE = 'File';

    const SESSION_DRIVER_REDIS = 'Redis';

    const SESSION_DRIVER_NULL = 'Null';

    /**
     * Get CApi_Runner instance
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
}
