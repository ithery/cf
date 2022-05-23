<?php
/**
 * @internal
 */
final class CVendor_Firebase_Util_JSON {
    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * Shamelessly copied from Guzzle.
     *
     * @param mixed            $value   The value being encoded
     * @param null|int<0, max> $options JSON encode option bitmask
     * @param null|int<1, max> $depth   Set the maximum depth. Must be greater than zero
     *
     * @see \GuzzleHttp\json_encode()
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException if the JSON cannot be encoded
     */
    public static function encode($value, $options = null, $depth = null) {
        if ($options == null) {
            $options = 0;
        }
        if ($depth == null) {
            $depth = 512;
        }

        try {
            return \json_encode($value, JSON_THROW_ON_ERROR | $options, $depth);
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('json_encode error: ' . $e->getMessage());
        }
    }

    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * Shamelessly copied from Guzzle.
     *
     * @param string           $json    JSON data to parse
     * @param null|bool        $assoc   When true, returned objects will be converted into associative arrays
     * @param null|int<1, max> $depth   User specified recursion depth
     * @param null|int<0, max> $options Bitmask of JSON decode options
     *
     * @see \GuzzleHttp\json_encode()
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException if the JSON cannot be decoded
     *
     * @return mixed
     */
    public static function decode(string $json, ?bool $assoc = null, ?int $depth = null, ?int $options = null) {
        $assoc ??= false;
        $depth ??= 512;
        $options ??= 0;

        try {
            return \json_decode($json, $assoc, $depth, JSON_THROW_ON_ERROR | $options);
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('json_decode error: ' . $e->getMessage());
        }
    }

    /**
     * Returns true if the given value is a valid JSON string.
     *
     * @param mixed $value
     *
     * @return bool
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
     *
     * @return string
     */
    public static function prettyPrint($value) {
        return self::encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
