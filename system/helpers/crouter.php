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
     * @deprecated since 1.3, use c::router()->current()->getController();
     */
    public static function controller() {
        return c::router()->current()->getController();
    }

    public static function controller_dir() {
        return c::router()->current()->getRouteData()->getControllerDir();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function method() {
        return c::router()->current()->getRouteData()->getMethod();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function routed_uri() {
        return c::router()->current()->getRouteData()->getRoutedUri();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function complete_uri() {
        return c::router()->current()->getRouteData()->getCompleteUri();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function query_string() {
        return c::router()->current()->getRouteData()->getQueryString();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function current_uri() {
        return c::router()->current()->getRouteData()->getUri();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function urlSuffix() {
        return c::router()->current()->getRouteData()->getUrlSuffix();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function segments() {
        return c::router()->current()->getRouteData()->getSegments();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function controller_path() {
        return c::router()->current()->getRouteData()->getControllerPath();
    }

    /**
     * @return string
     *
     * @deprecated since 1.3, dont use anymore, build from c::request
     */
    public static function arguments() {
        return c::router()->current()->getRouteData()->getArguments();
    }
}
