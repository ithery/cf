<?php
defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
class crouter {
    /**
     * @return string
     *
     * @deprecated since 1.2, use CF::domain
     */
    public static function domain() {
        return CF::domain();
    }

    /**
     * @return string
     *
     * @deprecated since 1.2, use CFRouter::getController()
     */
    public static function controller() {
        return CFRouter::getController();
    }

    public static function controller_dir() {
        return CFRouter::$controller_dir;
    }

    /**
     * @return string
     *
     * @deprecated since 1.2, use CFRouter::getControllerMethod()
     */
    public static function method() {
        return CFRouter::getControllerMethod();
    }

    public static function routed_uri() {
        return CFRouter::$routed_uri;
    }

    /**
     * @return string
     *
     * @deprecated since 1.2, use CFRouter::getCompleteUri()
     */
    public static function complete_uri() {
        return CFRouter::getCompleteUri();
    }

    public static function query_string() {
        return CFRouter::$query_string;
    }

    public static function current_uri() {
        return CFRouter::$current_uri;
    }

    public static function urlSuffix() {
        return CFRouter::$url_suffix;
    }

    public static function segments() {
        return CFRouter::$segments;
    }

    public static function controller_path() {
        return CFRouter::$controller_path;
    }

    public static function arguments() {
        return CFRouter::$arguments;
    }
}
