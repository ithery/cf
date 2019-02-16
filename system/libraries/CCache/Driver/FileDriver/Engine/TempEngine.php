<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:51:23 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache_Driver_FileDriver_Engine_TempEngine extends CCache_Driver_FileDriver_EngineAbstract {

    protected $tempFiles;
    protected $directory;

    public function __construct($options) {
        parent::__construct($options);
        $this->directory = $this->getOption('directory', 'default');
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
            $path = 'cache/' . trim($this->directory, '/') . '/' . implode('/', $parts) . '/' . $hash . '.cache';
            $this->tempFiles[$key] = CTemporary::createFile($path);
        }
        return $this->tempFiles[$key];
    }

    public function path($key) {
        return $this->getTempFiles($key)->getPath();
    }

    public function get($key, $lock = false) {

        return $this->getTempFiles($key)->get($lock);
    }

    public function put($key, $content, $lock = false) {
        return $this->getTempFiles($key)->put($content, $lock);
    }

    public function exists($key) {
        return $this->getTempFiles($key)->exists();
    }

    public function delete($key) {
        return $this->getTempFiles($key)->delete();
    }

}
