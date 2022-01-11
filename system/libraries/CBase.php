<?php

class CBase {
    const ENVIRONMENT_PRODUCTION = 'production';

    const ENVIRONMENT_DEVELOPMENT = 'development';

    const ENVIRONMENT_STAGING = 'staging';

    const ENVIRONMENT_TESTING = 'testing';

    /**
     * CF Session.
     *
     * @var CSession_Store
     */
    private static $session;

    public static function createRecursionContext() {
        return new CBase_RecursionContext();
    }

    public static function createStringParamable($string, array $params = []) {
        return new CBase_StringParamable($string, $params);
    }

    public static function session() {
        if (static::$session == null && CSession::sessionConfigured()) {
            $request = CHTTP::request();
            CSession::manager()->applyNativeSession();

            static::$session = c::tap(CSession::manager()->createStore(), function ($session) use ($request) {
                $session->setId($request->cookies->get($session->getName()));
                $session->setRequestOnHandler($request);
                $session->start();
            });
        }

        return static::$session;
    }
}
