<?php
/*
namespace React\Promise;
*/
interface React_Promise_PromiseInterface
{
    /**
     * @return PromiseInterface
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null);

    /**
     * @return void
     */
    public function done(callable $onFulfilled = null, callable $onRejected = null);

    /**
     * @return PromiseInterface
     */
    public function otherwise(callable $onRejected);

    /**
     * @return PromiseInterface
     */
    public function always(callable $onFulfilledOrRejected);

    /**
     * @return void
     */
    public function cancel();
}
