<?php

class CQC_Phpcsfixer {
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function isInstalled() {
        $phpcsfixerPharExists = file_exists(static::phpcsfixerPhar());
        $phpcsfixerConfigurationExists = file_exists(static::phpcsfixerConfiguration());

        return $phpcsfixerPharExists && $phpcsfixerConfigurationExists;
    }

    public static function phpcsfixerPhar() {
        return DOCROOT . '.bin' . DS . 'php-cs-fixer' . DS . 'php-cs-fixer.phar';
    }


    public static function phpcsfixerConfiguration() {
        if (CF::appCode() == null) {
            //do nothing CF already have phpcs.xml
        }

        return c::appRoot() . '.php-cs-fixer.dist.php';
    }
}
