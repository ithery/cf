<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 13, 2018, 10:51:31 AM
 */
final class CHelper {
    /**
     * @return \CHelper_File
     */
    public static function file() {
        return new CHelper_File();
    }

    /**
     * @return \CHelper_Formatter
     */
    public static function formatter() {
        return new CHelper_Formatter();
    }

    /**
     * @return \CHelper_Base64
     */
    public static function base64() {
        return new CHelper_Base64();
    }

    /**
     * @return \CHelper_JSON
     */
    public static function json() {
        return new CHelper_JSON();
    }

    /**
     * @return \CHelper_Request
     */
    public static function request() {
        return new CHelper_Request();
    }

    /**
     * @return \CHelper_Transform
     */
    public static function transform() {
        return new CHelper_Transform();
    }

    /**
     * @return \CHelper_Closure
     */
    public static function closure() {
        return new CHelper_Closure();
    }
}
