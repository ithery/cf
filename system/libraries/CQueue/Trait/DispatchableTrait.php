<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 2:46:55 AM
 */
trait CQueue_Trait_DispatchableTrait {
    /**
     * Dispatch the job with the given arguments.
     *
     * @return CQueue_PendingDispatch
     */
    public static function dispatch() {
        return new CQueue_PendingDispatch(new static(...func_get_args()));
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @return mixed
     */
    public static function dispatchNow() {
        return CQueue::dispatcher()->dispatchNow(new static(...func_get_args()));
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param array $chain
     *
     * @return \Illuminate\Foundation\Bus\PendingChain
     */
    public static function withChain($chain) {
        return new CQueue_PendingChain(static::class, $chain);
    }
}
