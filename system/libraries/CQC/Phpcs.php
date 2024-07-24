<?php

class CQC_Phpcs {
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function isInstalled() {
        $phpcsPharExists = file_exists(static::phpcsPhar());
        $phpcbfPharExists = file_exists(static::phpcbfPhar());
        $phpcsConfigurationExists = file_exists(static::phpcsConfiguration());

        return $phpcsPharExists && $phpcbfPharExists && $phpcsConfigurationExists;
    }

    public static function phpcsPhar() {
        return DOCROOT . '.bin' . DS . 'phpcs' . DS . 'phpcs.phar';
    }

    public static function phpcbfPhar() {
        return DOCROOT . '.bin' . DS . 'phpcs' . DS . 'phpcbf.phar';
    }

    public static function phpcsAppConfiguration() {
        if (CF::appCode() == null) {
            return null;
        }

        $appConfiguration = c::appRoot() . 'phpcs.xml';

        return $appConfiguration;
    }

    public static function phpcsConfiguration() {
        $cfConfiguration = DOCROOT . 'phpcs.xml';
        if (CF::appCode() == null) {
            return $cfConfiguration;
        }
        $appConfiguration = self::phpcsAppConfiguration();

        return CFile::exists($appConfiguration) ? $appConfiguration : $cfConfiguration;
    }
}
