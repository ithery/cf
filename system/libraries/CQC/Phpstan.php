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
     * 2026-08-18. Tetap di jalur 1.x: 2.x membuang
     * `ParametersAcceptorSelector::selectSingle()` dan `TypeUtils`, yang
     * dipakai 11 dari 56 berkas CQC_Phpstan, dan mewajibkan identifier pada
     * tiap rule. Naik ke sana pekerjaan tersendiri, bukan sekadar mengganti
     * phar - lihat docs/TODO.md.
     *
     * @var string
     */
    const VERSION = '1.12.0';

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
