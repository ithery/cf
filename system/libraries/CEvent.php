<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 12:16:41 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CEvent {

    protected static $dispatcher;

    /**
     * 
     * @return CEvent_Dispatcher
     */
    public static function dispatcher() {
        if (self::$dispatcher == null) {
            self::$dispatcher = static::createDispatcher();
        }
        return self::$dispatcher;
    }

    /**
     * 
     * @return CEvent_Dispatcher
     */
    public static function createDispatcher() {
        return new CEvent_Dispatcher();
    }
    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public static function dispatch() {
        $args = func_get_args();
        $event = carr::get($args, 0);
        $payload = array_slice($args, 1);
        static::dispatcher()->dispatch($event, $payload);
    }

}
