<?php

class CImage_Exception_InvalidManipulationException extends Exception {
    public static function invalidWidth($width) {
        return new self("Width should be a positive number. `{$width}` given.");
    }

    public static function invalidHeight($height) {
        return new self("Height should be a positive number. `{$height}` given.");
    }

    public static function invalidParameter($name, $invalidValue, array $validValues) {
        $validValues = self::formatValues($validValues);
        $name = ucfirst($name);

        return new self("{$name} should be one of {$validValues}. `{$invalidValue}` given.");
    }

    public static function valueNotInRange($name, $invalidValue, $minValue, $maxValue) {
        $name = ucfirst($name);

        return new self("{$name} should be a number in the range {$minValue} until {$maxValue}. `{$invalidValue}` given.");
    }

    protected static function formatValues(array $values) {
        $quotedValues = array_map(function ($value) {
            return "`{$value}`";
        }, $values);

        return implode(', ', $quotedValues);
    }
}
