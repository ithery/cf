<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2019, 1:01:29 AM
 */
trait CEvent_Trait_Dispatchable {
    /**
     * Dispatch the event with the given arguments.
     *
     * @return void
     */
    public static function dispatch() {
        return c::event(new static(...func_get_args()));
    }

    /**
     * Dispatch the event with the given arguments if the given truth test passes.
     *
     * @param bool  $boolean
     * @param mixed ...$arguments
     *
     * @return void
     */
    public static function dispatchIf($boolean, ...$arguments) {
        if ($boolean) {
            return c::event(new static(...$arguments));
        }
    }

    /**
     * Dispatch the event with the given arguments unless the given truth test passes.
     *
     * @param bool  $boolean
     * @param mixed ...$arguments
     *
     * @return void
     */
    public static function dispatchUnless($boolean, ...$arguments) {
        if (!$boolean) {
            return c::event(new static(...$arguments));
        }
    }

    /**
     * Broadcast the event with the given arguments.
     *
     * @return \CBroadcasting_PendingBroadcast
     */
    public static function broadcast() {
        return c::broadcast(new static(...func_get_args()));
    }
}
