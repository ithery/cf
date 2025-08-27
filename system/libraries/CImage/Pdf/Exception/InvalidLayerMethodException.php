<?php

class CImage_Pdf_Exception_InvalidLayerMethodException extends Exception {
    public static function for(int $value) {
        return new static("Invalid layer method value ({$value}).");
    }
}
