<?php

defined('SYSPATH') or die('No direct access allowed.');

final class CHelper {
    /**
     * @return \CHelper_File
     *
     * @deprecated 1.2, use CFile
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
     *
     * @deprecated since 1.3
     */
    public static function closure() {
        return new CHelper_Closure();
    }
}
