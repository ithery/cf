<?php

class CQC_Phpstan {
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function isInstalled() {
        $phpstanBinaryExists = file_exists(static::phpstanBinary());
        $phpstanPharExists = file_exists(static::phpstanPhar());

        return $phpstanBinaryExists
            && $phpstanPharExists;
    }

    public static function phpstanBinary() {
        return DOCROOT . '.bin' . DS . 'phpunit' . DS . 'phpstan';
    }

    public static function phpstanPhar() {
        return DOCROOT . '.bin' . DS . 'phpunit' . DS . 'phpstan.phar';
    }
}
