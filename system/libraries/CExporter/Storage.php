<?php

class CExporter_Storage {
    /**
     * @var CExporter_Storage
     */
    private static $instance;

    /**
     * @return CExporter_Storage
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct() {
    }

    /**
     * @param null|string $disk
     * @param array       $diskOptions
     *
     * @return CExporter_Disk
     */
    public function disk($disk = null, array $diskOptions = []) {
        return new CExporter_Disk(
            CStorage::instance()->disk($disk),
            $disk,
            $diskOptions
        );
    }
}
