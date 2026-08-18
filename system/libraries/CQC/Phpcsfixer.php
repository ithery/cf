<?php

class CQC_Phpcsfixer {
    /**
     * Versi php-cs-fixer yang didukung.
     *
     * Berjalan pada PHP 7.4 sampai 8.4. Versi 3.13.0 yang dipakai sebelumnya
     * punya batas atas PHP 8.1 dan menolak jalan di atas itu.
     *
     * @var string
     */
    const VERSION = '3.95.18';

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

    /**
     * Versi phar yang terpasang, null bila belum ada atau tidak terbaca.
     *
     * @return null|string
     */
    public static function installedVersion() {
        return CQC::pharVersion(static::phpcsfixerPhar());
    }

    /**
     * @return bool
     */
    public static function isVersionSupported() {
        return static::installedVersion() === static::VERSION;
    }

    public static function phpcsfixerAppConfiguration() {
        if (CF::appCode() == null) {
            return null;
        }

        $appConfiguration = c::appRoot() . '.php-cs-fixer.dist.php';

        return $appConfiguration;
    }

    public static function phpcsfixerConfiguration() {
        $cfConfiguration = DOCROOT . '.php-cs-fixer.dist.php';
        if (CF::appCode() == null) {
            return $cfConfiguration;
        }
        $appConfiguration = self::phpcsfixerAppConfiguration();

        return CFile::exists($appConfiguration) ? $appConfiguration : $cfConfiguration;
    }
}
