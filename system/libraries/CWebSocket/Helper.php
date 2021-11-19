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

    /**
     * Implode an array with the key and value pair giving
     * a glue, a separator between pairs and the array
     * to implode.
     *
     * @param string       $glue      The glue between key and value
     * @param string       $separator Separator between pairs
     * @param array|string $array     The array to implode
     *
     * @return string The imploded array
     */
    public static function pusherArrayImplode($glue, $separator, $array) {
        if (!is_array($array)) {
            return $array;
        }

        $string = [];
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
            $string[] = "{$key}{$glue}{$val}";
        }

        return implode($separator, $string);
    }
}
