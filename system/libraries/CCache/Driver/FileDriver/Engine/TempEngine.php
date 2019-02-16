<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:51:23 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache_Driver_FileDriver_Engine_TempEngine extends CCache_Driver_FileDriver_EngineAbstract {

    protected $tempFiles;

    public function __construct($options) {
        parent::__construct($options);
        $this->tempFiles = array();
    }

    /**
     * 
     * @param string $key
     * @return CTemporary_File
     */
    public function getTempFiles($key) {
        if (!isset($this->tempFiles[$key])) {
            $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);
            $path = 'cache' . '/' . implode('/', $parts) . '/';
            $filename = $hash . '.cache';
            $this->tempFiles[$key] = CTemporary::createFile($path, $filename)->getPath();
        }
        return $this->tempFiles[$key];
    }

    public function path($key) {
        $this->getTempFiles($key)->getPath();
    }

    public function get($key) {
        $this->getTempFiles($key)->get();
    }

    public function put($key, $content) {
        $this->getTempFiles($key)->put($content);
    }

}
