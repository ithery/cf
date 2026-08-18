<?php

class CImage_Pdf_Exception_InvalidSizeException extends Exception {
    public static function for(int $value, string $type, string $property) {
        return new static(ucfirst($type) . " {$property} must be greater than or equal to 0, {$value} given.");
    }

    public static function forThumbnail(int $value, string $property) {
        return static::for($value, 'thumbnail', $property);
    }

    public static function forImage(int $value, string $property) {
        return static::for($value, 'image', $property);
    }
}
