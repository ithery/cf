<?php

class CDocs_PhpDocumentor {
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function isInstalled() {
        $phpDocumentorPharExists = file_exists(static::phpDocumentorPhar());
        $phpDocumentorConfigurationExists = file_exists(static::phpDocumentorConfiguration());

        return $phpDocumentorPharExists && $phpDocumentorConfigurationExists;
    }

    public static function phpDocumentorPhar() {
        return DOCROOT . '.bin' . DS . 'phpDocumentor' . DS . 'phpDocumentor.phar';
    }

    public static function phpDocumentorConfiguration() {
        if (CF::appCode() == null) {
            //do nothing CF already have phpcs.xml
        }

        return c::appRoot() . 'phpdoc.dist.xml';
    }
}
