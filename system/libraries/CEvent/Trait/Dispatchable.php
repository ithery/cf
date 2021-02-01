<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2019, 1:01:29 AM
 */
trait CEvent_Trait_Dispatchable {
    protected static $dispatcher;

    protected static function getDispatcher() {
        if (self::$dispatcher == null) {
            self::$dispatcher = CEvent::createDispatcher();
        }
        return self::$dispatcher;
    }

    /**
     * Dispatch the event with the given arguments.
     *
     * @return void
     */
    public static function dispatch() {
        $dispatcher = self::getDispatcher();
        return call_user_func_array([$dispatcher, 'dispatch'], func_get_args());
    }

    /**
     * Listen the event with the given arguments.
     *
     * @return void
     */
    public static function listen() {
        $dispatcher = self::getDispatcher();
        return call_user_func_array([$dispatcher, 'listen'], func_get_args());
    }

    /**
     * Broadcast the event with the given arguments.
     *
     * @return \Illuminate\Broadcasting\PendingBroadcast
     */
    public static function broadcast() {
        return broadcast(new static(...func_get_args()));
    }
}
