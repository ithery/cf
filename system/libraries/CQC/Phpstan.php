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
        $phpstanConfigurationExists = file_exists(static::phpstanConfiguration());
        $phpstanBootstrapExists = file_exists(static::phpstanBootstrap());

        return $phpstanBinaryExists
            && $phpstanPharExists
            && $phpstanConfigurationExists
            && $phpstanBootstrapExists;
    }

    public static function phpstanBinary() {
        return DOCROOT . '.bin' . DS . 'phpstan' . DS . 'phpstan';
    }

    public static function phpstanConfiguration() {
        return c::appRoot() . 'phpstan.neon';
    }

    public static function phpstanBootstrap() {
        return c::appRoot() . 'phpstan-bootstrap.php';
    }

    public static function phpstanPhar() {
        return DOCROOT . '.bin' . DS . 'phpstan' . DS . 'phpstan.phar';
    }

    /**
     * @param null|string $directory
     *
     * @return CQC_Phpstan_Runner
     */
    public static function createRunner($directory = null) {
        return new CQC_Phpstan_Runner($directory);
    }
}
