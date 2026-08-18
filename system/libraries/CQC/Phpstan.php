<?php

/**
 * @see https://github.com/larastan/larastan
 * @see CQC
 */
class CQC_Phpstan {
    /**
     * Versi PHPStan yang didukung.
     *
     * Berjalan pada PHP 7.4 sampai 8.x - diuji langsung di lsphp74 dan 8.2,
     * 2026-08-18. Dipilih di jalur 2.2 karena itu yang dituntut Larastan 3.10
     * (`phpstan/phpstan ^2.2.2`) yang jadi acuan porting di
     * `reference/larastan`, sehingga kode di sana dapat dibandingkan langsung
     * tanpa menerjemahkan API lebih dulu.
     *
     * @var string
     */
    const VERSION = '2.2.8';

    /**
     * @var CQC_Phpstan
     */
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
        if (CF::appCode() == null) {
            return DOCROOT . 'phpstan.neon';
        }

        return c::appRoot() . 'phpstan.neon';
    }

    public static function phpstanBootstrap() {
        if (CF::appCode() == null) {
            return DOCROOT . 'system' . DS . 'core' . DS . 'BootstrapPhpstan.php';
        }

        return c::appRoot() . 'phpstan-bootstrap.php';
    }

    public static function phpstanPhar() {
        return DOCROOT . '.bin' . DS . 'phpstan' . DS . 'phpstan.phar';
    }

    /**
     * Versi phar yang terpasang, null bila belum ada atau tidak terbaca.
     *
     * @param null|string $pharPath
     *
     * @return null|string
     */
    public static function installedVersion($pharPath = null) {
        return CQC::pharVersion($pharPath == null ? static::phpstanPhar() : $pharPath);
    }

    /**
     * @param null|string $pharPath
     *
     * @return bool
     */
    public static function isVersionSupported($pharPath = null) {
        return static::installedVersion($pharPath) === static::VERSION;
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
