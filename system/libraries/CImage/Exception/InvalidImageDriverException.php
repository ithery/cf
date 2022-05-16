<?php

class CImage_Exception_InvalidImageDriverException extends Exception {
    public static function driver($driver) {
        return new self("Driver must be `gd` or `imagick`. `{$driver}` provided.");
    }
}
