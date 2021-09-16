<?php
/**
 * Conversion Exception is thrown when the database to PHP conversion fails.
 *
 * @psalm-immutable
 */
class CDatabase_Schema_Exception_ConversionException extends Exception {
    /**
     * Thrown when a Database to Doctrine Type Conversion fails.
     *
     * @param string     $value
     * @param string     $toType
     * @param null|mixed $previous
     *
     * @return CDatabase_Schema_Exception_ConversionException
     */
    public static function conversionFailed($value, $toType, $previous = null) {
        $value = strlen($value) > 32 ? substr($value, 0, 20) . '...' : $value;

        return new self('Could not convert database value "' . $value . '" to Doctrine Type ' . $toType, 0, $previous);
    }

    /**
     * Thrown when a Database to Doctrine Type Conversion fails and we can make a statement
     * about the expected format.
     *
     * @param string     $value
     * @param string     $toType
     * @param string     $expectedFormat
     * @param null|mixed $previous
     *
     * @return CDatabase_Schema_Exception_ConversionException
     */
    public static function conversionFailedFormat($value, $toType, $expectedFormat, $previous = null) {
        $value = strlen($value) > 32 ? substr($value, 0, 20) . '...' : $value;

        return new self(
            'Could not convert database value "' . $value . '" to Doctrine Type '
            . $toType . '. Expected format: ' . $expectedFormat,
            0,
            $previous
        );
    }

    /**
     * Thrown when the PHP value passed to the converter was not of the expected type.
     *
     * @param mixed      $value
     * @param string     $toType
     * @param string[]   $possibleTypes
     * @param null|mixed $previous
     *
     * @return CDatabase_Schema_Exception_ConversionException
     */
    public static function conversionFailedInvalidType(
        $value,
        $toType,
        array $possibleTypes,
        $previous = null
    ) {
        if (is_scalar($value) || $value === null) {
            return new self(sprintf(
                'Could not convert PHP value %s to type %s. Expected one of the following types: %s',
                var_export($value, true),
                $toType,
                implode(', ', $possibleTypes)
            ), 0, $previous);
        }

        return new self(sprintf(
            'Could not convert PHP value of type %s to type %s. Expected one of the following types: %s',
            is_object($value) ? get_class($value) : gettype($value),
            $toType,
            implode(', ', $possibleTypes)
        ), 0, $previous);
    }

    /**
     * @param mixed  $value
     * @param string $format
     * @param string $error
     *
     * @return CDatabase_Schema_Exception_ConversionException
     */
    public static function conversionFailedSerialization($value, $format, $error) {
        $actualType = is_object($value) ? get_class($value) : gettype($value);

        return new self(sprintf(
            "Could not convert PHP type '%s' to '%s', as an '%s' error was triggered by the serialization",
            $actualType,
            $format,
            $error
        ));
    }

    public static function conversionFailedUnserialization($format, $error) {
        return new self(sprintf(
            "Could not convert database value to '%s' as an error was triggered by the unserialization: '%s'",
            $format,
            $error
        ));
    }
}
