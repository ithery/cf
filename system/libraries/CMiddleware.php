<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 7, 2018, 11:34:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CMiddleware {

    
    /**
     * 
     * @return CMiddleware_Manager
     */
    public static function manager() {
        return CMiddleware_Manager::instance();
    }

    
    public static function middleware() {
   
        return static::manager()->getMiddleware();
    }
}
