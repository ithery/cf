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
     * Dispatch the job with the given arguments if the given truth test passes.
     *
     * @param bool|\Closure $boolean
     * @param mixed         ...$arguments
     *
     * @return \CQueue_PendingDispatch|\CBase_Fluent
     */
    public static function dispatchIf($boolean, ...$arguments) {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return c::value($boolean, $dispatchable)
                ? new CQueue_PendingDispatch($dispatchable)
                : new CBase_Fluent();
        }

        return c::value($boolean)
            ? new CQueue_PendingDispatch(new static(...$arguments))
            : new CBase_Fluent();
    }

    /**
     * Dispatch the job with the given arguments unless the given truth test passes.
     *
     * @param bool|\Closure $boolean
     * @param mixed         ...$arguments
     *
     * @return \CQueue_PendingDispatch|\CBase_Fluent
     */
    public static function dispatchUnless($boolean, ...$arguments) {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return !c::value($boolean, $dispatchable)
                ? new CQueue_PendingDispatch($dispatchable)
                : new CBase_Fluent();
        }

        return !c::value($boolean)
            ? new CQueue_PendingDispatch(new static(...$arguments))
            : new CBase_Fluent();
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @return mixed
     */
    public static function dispatchSync(...$arguments) {
        return CQueue::dispatcher()->dispatchSync(new static(...$arguments));
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
     * Dispatch a command to its appropriate handler after the current process.
     *
     * @return mixed
     */
    public static function dispatchAfterResponse(...$arguments) {
        return CQueue::dispatcher()->dispatchAfterResponse(new static(...$arguments));
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param array $chain
     *
     * @return \CQueue_PendingChain
     */
    public static function withChain($chain) {
        return new CQueue_PendingChain(static::class, $chain);
    }
}
