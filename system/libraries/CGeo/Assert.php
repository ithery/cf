<?php
class CGeo_Assert {
    /**
     * @param float  $value
     * @param string $message
     */
    public static function latitude($value, $message = '') {
        self::float($value, $message);
        if ($value < -90 || $value > 90) {
            throw new CGeo_Exception_InvalidArgument(sprintf($message ?: 'Latitude should be between -90 and 90. Got: %s', $value));
        }
    }

    /**
     * @param float  $value
     * @param string $message
     */
    public static function longitude($value, $message = '') {
        self::float($value, $message);
        if ($value < -180 || $value > 180) {
            throw new CGeo_Exception_InvalidArgument(sprintf($message ?: 'Longitude should be between -180 and 180. Got: %s', $value));
        }
    }

    /**
     * @param mixed  $value
     * @param string $message
     */
    public static function notNull($value, $message = '') {
        if (null === $value) {
            throw new CGeo_Exception_InvalidArgument(sprintf($message ?: 'Value cannot be null'));
        }
    }

    private static function typeToString($value) {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    /**
     * @param $value
     * @param $message
     */
    private static function float($value, $message) {
        if (!is_float($value)) {
            throw new CGeo_Exception_InvalidArgument(sprintf($message ?: 'Expected a float. Got: %s', self::typeToString($value)));
        }
    }
}
