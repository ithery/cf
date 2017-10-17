<?php

require_once dirname(__FILE__) . DS . 'functions.php';
/*
  namespace React\Promise;
 */

final class React_Promise_FulfilledPromise implements React_Promise_PromiseInterface {

    private $value;

    public function __construct($value = null) {
        if ($value instanceof PromiseInterface) {
            throw new \InvalidArgumentException('You cannot create React\Promise\FulfilledPromise with a promise. Use React\Promise\resolve($promiseOrValue) instead.');
        }

        $this->value = $value;
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null) {
        if (null === $onFulfilled) {
            return $this;
        }

        return new React_Promise_Promise(function (callable $resolve, callable $reject) use ($onFulfilled) {
            react_promise_enqueue(function () use ($resolve, $reject, $onFulfilled) {
                try {
                    $resolve($onFulfilled($this->value));
                } catch (\Throwable $exception) {
                    $reject($exception);
                } catch (\Exception $exception) {
                    $reject($exception);
                }
            });
        });
    }

    public function done(callable $onFulfilled = null, callable $onRejected = null) {
        if (null === $onFulfilled) {
            return;
        }

        react_promise_enqueue(function () use ($onFulfilled) {
            try {
                $result = $onFulfilled($this->value);
            } catch (\Throwable $exception) {
                return fatalError($exception);
            } catch (\Exception $exception) {
                return fatalError($exception);
            }

            if ($result instanceof PromiseInterface) {
                $result->done();
            }
        });
    }

    public function otherwise(callable $onRejected) {
        return $this;
    }

    public function always(callable $onFulfilledOrRejected) {
        return $this->then(function ($value) use ($onFulfilledOrRejected) {
                    return react_promise_resolve($onFulfilledOrRejected())->then(function () use ($value) {
                                return $value;
                            });
                });
    }

    public function cancel() {
        
    }

}
