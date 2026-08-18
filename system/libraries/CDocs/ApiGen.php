<?php

class CDocs_ApiGen {
    private static $instance;

    /**
     * @return CDocs_ApiGen
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Check apigen is installed.
     *
     * @return bool
     */
    public function isInstalled() {
        $apiGenPharExists = file_exists(static::apiGenPhar());
        $apiGenConfigurationExists = file_exists(static::apiGenConfiguration());

        return $apiGenPharExists && $apiGenConfigurationExists;
    }

    /**
     * Get apigen path phar file.
     *
     * @return string
     */
    public static function apiGenPhar() {
        return DOCROOT . '.bin' . DS . 'apigen' . DS . 'apigen.phar';
    }

    /**
     * Get apigen.neon configuration path.
     *
     * @return null|string
     */
    public static function apiGenConfiguration() {
        if (CF::appCode() == null) {
            //do nothing CF already have phpcs.xml
        }

        return c::appRoot() . 'apigen.neon';
    }
}
