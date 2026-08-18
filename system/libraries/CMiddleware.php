<?php

defined('SYSPATH') or die('No direct access allowed.');

final class CMiddleware {
    /**
     * @return CMiddleware_Manager
     */
    public static function manager() {
        return CMiddleware_Manager::instance();
    }

    public static function middleware() {
        return static::manager()->getMiddleware();
    }
}
