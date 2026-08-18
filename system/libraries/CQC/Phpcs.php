<?php

class CQC_Phpcs {
    /**
     * Versi PHP_CodeSniffer yang didukung.
     *
     * Berjalan pada PHP 7.4 sampai 8.4. Tetap di jalur 3.x: 4.x menegakkan
     * PEAR.NamingConventions.ValidClassName lebih ketat, dan seluruh CF memakai
     * nama berkelas awalan-dengan-garis-bawah yang ditolaknya.
     *
     * @var string
     */
    const VERSION = '3.13.6';

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

    /**
     * Versi phar yang terpasang, null bila belum ada atau tidak terbaca.
     *
     * @param null|string $pharPath phpcs.phar bila tidak diisi
     *
     * @return null|string
     */
    public static function installedVersion($pharPath = null) {
        return CQC::pharVersion($pharPath == null ? static::phpcsPhar() : $pharPath);
    }

    /**
     * @param null|string $pharPath
     *
     * @return bool
     */
    public static function isVersionSupported($pharPath = null) {
        return static::installedVersion($pharPath) === static::VERSION;
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
