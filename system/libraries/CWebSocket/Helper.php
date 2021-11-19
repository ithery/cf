<?php

class CWebSocket_Helper {
    /**
     * The loop used to create the Fulfilled Promise.
     *
     * @var null|\React\EventLoop\LoopInterface
     */
    public static $loop = null;

    /**
     * Transform the Redis' list of key after value
     * to key-value pairs.
     *
     * @param array $list
     *
     * @return array
     */
    public static function redisListToArray(array $list) {
        // Redis lists come into a format where the keys are on even indexes
        // and the values are on odd indexes. This way, we know which
        // ones are keys and which ones are values and their get combined
        // later to form the key => value array.
        list($keys, $values) = c::collect($list)->partition(function ($value, $key) {
            return $key % 2 === 0;
        });

        return array_combine($keys->all(), $values->all());
    }

    /**
     * Create a new fulfilled promise with a value.
     *
     * @param mixed $value
     *
     * @return \React\Promise\PromiseInterface
     */
    public static function createFulfilledPromise($value) {
        $resolver = CF::config(
            'websocket.promise_resolver',
            \React\Promise\FulfilledPromise::class
        );

        return new $resolver($value, static::$loop);
    }
}
