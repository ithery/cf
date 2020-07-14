<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Storage {

    /**
     *
     * @var CExporter_Storage 
     */
    private static $instance;

    /**
     * 
     * @return CExporter_Storage
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @var CStorage
     */
    private $storage;

    /**
     * @param Factory $filesystem
     */
    public function __construct() {
        $this->storage = CStorage::instance();
    }

    /**
     * @param string|null $disk
     * @param array       $diskOptions
     *
     * @return CExporter_Disk
     */
    public function disk($disk = null, array $diskOptions = []) {
        return new CExporter_Disk(
                $this->storage->disk($disk), $disk, $diskOptions
        );
    }

}
