<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 2, 2019, 10:23:36 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CHTTP {

    /**
     *
     * @var CHTTP_Request 
     */
    protected static $request;

    /**
     * 
     * @return CHTTP_Request
     */
    public static function request() {
        if (self::$request == null) {
            self::$request = CHTTP_Request::capture();
        }
        return self::$request;
    }

    /**
     * 
     * @return CHTTP_Response
     */
    public static function createResponse($content = '', $status = 200, array $headers = []) {
        return new CHTTP_Response($content, $status, $headers);
    }

    public static function refresh() {
        static::$request = null;
    }

}
