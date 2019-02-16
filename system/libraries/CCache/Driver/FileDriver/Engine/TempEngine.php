<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:51:23 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache_Driver_FileDriver_Engine_TempEngine extends CCache_Driver_FileDriver_EngineAbstract {

    protected $tempFile;

    public function __construct($key) {
        parent::__construct($key);

        //generate path
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);
        $path = 'cache' . '/' . implode('/', $parts) . '/';
        $filename = $hash . '.cache';
        $this->tempFile = CTemp::createFile($path, $filename);
    }

    public function path() {
        $this->tempFile->getPath();
    }

}
