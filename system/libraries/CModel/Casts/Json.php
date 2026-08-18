<?php

class CModel_Casts_Json {
    /**
     * The custom JSON encoder.
     *
     * @var null|callable
     */
    protected static $encoder;

    /**
     * The custom JSON decode.
     *
     * @var null|callable
     */
    protected static $decoder;

    /**
     * Encode the given value.
     *
     * @param mixed $value
     */
    public static function encode($value) {
        return isset(static::$encoder) ? (static::$encoder)($value) : json_encode($value);
    }

    /**
     * Decode the given value.
     *
     * @param mixed $value
     * @param bool  $associative
     */
    public static function decode($value, $associative = true) {
        return isset(static::$decoder)
                ? (static::$decoder)($value, $associative)
                : json_decode($value, $associative);
    }

    /**
     * Encode all values using the given callable.
     *
     * @param null|callable $encoder
     *
     * @return void
     */
    public static function encodeUsing($encoder) {
        static::$encoder = $encoder;
    }

    /**
     * Decode all values using the given callable.
     *
     * @param null|callable $decoder
     *
     * @return void
     */
    public static function decodeUsing($decoder) {
        static::$decoder = $decoder;
    }
}
