<?php

/*
  namespace React\Promise;
 */

final class React_Promise_RejectedPromise implements React_Promise_PromiseInterface {

    private $reason;

    public function __construct($reason = null) {
        if ($reason instanceof React_Promise_PromiseInterface) {
            throw new \InvalidArgumentException('You cannot create React\Promise\RejectedPromise with a promise. Use React\Promise\reject($promiseOrValue) instead.');
        }

        $this->reason = $reason;
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null) {
        if (null === $onRejected) {
            return $this;
        }

        return new React_Promise_Promise(function (callable $resolve, callable $reject) use ($onRejected) {
            react_promise_enqueue(function () use ($resolve, $reject, $onRejected) {
                try {
                    $resolve($onRejected($this->reason));
                } catch (\Throwable $exception) {
                    $reject($exception);
                } catch (\Exception $exception) {
                    $reject($exception);
                }
            });
        });
    }

    public function done(callable $onFulfilled = null, callable $onRejected = null) {
        react_promise_enqueue(function () use ($onRejected) {
            if (null === $onRejected) {
                return fatalError(
                        React_Promise_UnhandledRejectionException::resolve($this->reason)
                );
            }

            try {
                $result = $onRejected($this->reason);
            } catch (\Throwable $exception) {
                return fatalError($exception);
            } catch (\Exception $exception) {
                return fatalError($exception);
            }

            if ($result instanceof self) {
                return react_promise_fatalError(
                        React_Promise_UnhandledRejectionException::resolve($result->reason)
                );
            }

            if ($result instanceof React_Promise_PromiseInterface) {
                $result->done();
            }
        });
    }

    public function otherwise(callable $onRejected) {
        if (!react_promise__checkTypehint($onRejected, $this->reason)) {
            return $this;
        }

        return $this->then(null, $onRejected);
    }

    public function always(callable $onFulfilledOrRejected) {
        return $this->then(null, function ($reason) use ($onFulfilledOrRejected) {
                    return react_promise_resolve($onFulfilledOrRejected())->then(function () use ($reason) {
                                return new React_Promise_RejectedPromise($reason);
                            });
                });
    }

    public function cancel() {
        
    }

}
