<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 12:16:41 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CEvent {

    /**
     * 
     * @return CEvent_Dispatcher
     */
    public static function createDispatcher() {
        return new CEvent_Dispatcher();
    }

    public static function dispatch() {
        $args = func_get_args();
        $event = carr::get($args, 0);
        $payload = array_slice($args, 1);
        static::createDispatcher()->dispatch($event, $payload);
    }

}
