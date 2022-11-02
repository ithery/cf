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
        $phpstanConfiguratioExists = file_exists(static::phpstanConfiguration());

        return $phpstanBinaryExists
            && $phpstanPharExists
            && $phpstanConfiguratioExists;
    }

    public static function phpstanBinary() {
        return DOCROOT . '.bin' . DS . 'phpstan' . DS . 'phpstan';
    }

    public static function phpstanConfiguration() {
        return c::appRoot() . 'phpstan.neon';
    }

    public static function phpstanPhar() {
        return DOCROOT . '.bin' . DS . 'phpstan' . DS . 'phpstan.phar';
    }
}
