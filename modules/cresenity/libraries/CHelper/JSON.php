<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 17, 2018, 12:20:01 AM
 */

/**
 * JSON tools.
 */
class CHelper_JSON {
    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * Shamelessly copied from Guzzle.
     *
     * @param mixed $value   The value being encoded
     * @param int   $options JSON encode option bitmask
     * @param int   $depth   Set the maximum depth. Must be greater than zero
     *
     * @see \GuzzleHttp\json_encode()
     *
     * @throws InvalidArgumentException if the JSON cannot be encoded
     */
    public static function encode($value, $options = null, $depth = null) {
        if ($options == null) {
            $options = 0;
        }
        if ($depth == null) {
            $depth = 512;
        }

        $json = \json_encode($value, $options, $depth);
        if ($error = self::getJsonLastErrorMsg()) {
            throw new CHelper_Exception_JSONParseException($error);
        }

        return (string) $json;
    }

    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * Shamelessly copied from Guzzle.
     *
     * @param string $json    JSON data to parse
     * @param bool   $assoc   When true, returned objects will be converted into associative arrays
     * @param int    $depth   User specified recursion depth
     * @param int    $options Bitmask of JSON decode options
     *
     * @throws \InvalidArgumentException if the JSON cannot be decoded
     *
     * @see \GuzzleHttp\json_encode()
     *
     * @return mixed
     */
    public static function decode($json, $assoc = null, $depth = null, $options = null) {
        if ($assoc == null) {
            $assoc = false;
        }
        if ($depth == null) {
            $depth = 512;
        }
        if ($options == null) {
            $options = 0;
        }
        $data = \json_decode($json, $assoc, $depth, $options);
        if ($error = self::getJsonLastErrorMsg()) {
            throw new CHelper_Exception_JSONParseException($error);
        }

        return $data;
    }

    /**
     * Returns true if the given value is a valid JSON string.
     *
     * @param mixed $value
     */
    public static function isValid($value) {
        try {
            self::decode($value);

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     */
    public static function prettyPrint($value) {
        return self::encode($value, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    }

    /**
     * Parse JSON string to an array.
     *
     * @param string $args,... JSON string to parse
     *
     * @link http://php.net/manual/en/function.json-decode.php
     * @link http://php.net/manual/en/function.json-last-error.php
     *
     * @throws CHelper_Exception_JSONParseException
     *
     * @return array PHP array representation of JSON string
     */
    public static function parse($args/* inherit from json_decode */) {
        // extract arguments
        $args = func_get_args();
        // default to decoding into an assoc array
        if (count($args) === 1) {
            $args[] = true;
        }
        // run decode
        $array = call_user_func_array([__CLASS__, 'decode'], $args);
        // turn errors into exceptions for easier catching
        // output
        return $array;
    }

    /**
     * Convert input to JSON string with standard options.
     *
     * @param mixed $args,... Target to stringify
     *
     * @link http://php.net/manual/en/function.json-encode.php
     * @link http://php.net/manual/en/function.json-last-error.php
     *
     * @throws CHelper_Exception_JSONParseException
     *
     * @return string Valid JSON representation of $input
     */
    public static function stringify(/* inherit from json_encode */) {
        // extract arguments
        $args = func_get_args();
        return call_user_func_array([__CLASS__, 'encode'], $args);
    }

    /**
     * Get Json Last Error.
     *
     * @link http://php.net/manual/en/function.json-last-error.php
     * @link http://php.net/manual/en/function.json-last-error-msg.php
     * @link https://github.com/php/php-src/blob/master/ext/json/json.c#L308
     *
     * @return string
     */
    private static function getJsonLastErrorMsg() {
        return JSON_ERROR_NONE !== json_last_error() ? json_last_error_msg() : false;
    }
}
