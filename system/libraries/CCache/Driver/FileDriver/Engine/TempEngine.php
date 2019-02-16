<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:51:23 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache_Driver_FileDriver_Engine_TempEngine extends CCache_Driver_FileDriver_EngineAbstract {

    protected $tempFile;

    public function __construct($options) {
        parent::__construct($options);
    }

    public function path($key) {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);
        $path = 'cache' . '/' . implode('/', $parts) . '/';
        $filename = $hash . '.cache';
        return CTemporary::createFile($path, $filename)->getPath();
    }

}
