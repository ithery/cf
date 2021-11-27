<?php

namespace React\Promise\Timer;

use React\EventLoop\Loop;
use React\Promise\Promise;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;

final class Timer {
    public static function timeout(PromiseInterface $promise, $time, LoopInterface $loop = null) {
        // cancelling this promise will only try to cancel the input promise,
        // thus leaving responsibility to the input promise.
        $canceller = null;
        if (\method_exists($promise, 'cancel')) {
            // pass promise by reference to clean reference after cancellation handler
            // has been invoked once in order to avoid garbage references in call stack.
            $canceller = function () use (&$promise) {
                $promise->cancel();
                $promise = null;
            };
        }

        if ($loop === null) {
            $loop = Loop::get();
        }

        return new Promise(function ($resolve, $reject) use ($loop, $time, $promise) {
            $timer = null;
            $promise = $promise->then(function ($v) use (&$timer, $loop, $resolve) {
                if ($timer) {
                    $loop->cancelTimer($timer);
                }
                $timer = false;
                $resolve($v);
            }, function ($v) use (&$timer, $loop, $reject) {
                if ($timer) {
                    $loop->cancelTimer($timer);
                }
                $timer = false;
                $reject($v);
            });

            // promise already resolved => no need to start timer
            if ($timer === false) {
                return;
            }

            // start timeout timer which will cancel the input promise
            $timer = $loop->addTimer($time, function () use ($time, &$promise, $reject) {
                $reject(new TimeoutException($time, 'Timed out after ' . $time . ' seconds'));

                // try to invoke cancellation handler of input promise and then clean
                // reference in order to avoid garbage references in call stack.
                if (\method_exists($promise, 'cancel')) {
                    $promise->cancel();
                }
                $promise = null;
            });
        }, $canceller);
    }
}
