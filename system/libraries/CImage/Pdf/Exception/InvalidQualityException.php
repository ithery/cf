<?php

class CImage_Pdf_Exception_InvalidQualityException extends Exception {
    public static function for(int $value) {
        return new static("Quality must be between 1 and 100, {$value} given.");
    }
}
