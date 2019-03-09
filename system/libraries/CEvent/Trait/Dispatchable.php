<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 1:01:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait Dispatchable {

    /**
     * Dispatch the event with the given arguments.
     *
     * @return void
     */
    public static function dispatch() {
        CContainer::getInstance()->make($abstract, $parameters);
        return event(new static(...func_get_args()));
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
