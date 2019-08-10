<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 3:25:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use League\Flysystem\Cached\CachedAdapter;

class CManager_File_Connector_FileManager_FM_StorageRepository {

    private $disk;
    private $path;
    private $helper;

    public function __construct($storage_path, $helper) {
        $this->helper = $helper;
        $this->disk = CStorage::instance()->disk($this->helper->config('disk'));
        $this->path = $storage_path;
    }

    public function __call($function_name, $arguments) {
        // TODO: check function exists
        return $this->disk->$function_name($this->path, ...$arguments);
    }

    public function rootPath() {
        $adapter = $this->disk->getDriver()->getAdapter();
        if ($adapter instanceof CachedAdapter) {
            $adapter = $adapter->getAdapter();
        }
        return $adapter->getPathPrefix();
    }

    public function move($new_lfm_path) {
        return $this->disk->move($this->path, $new_lfm_path->path('storage'));
    }

    public function save($file) {
        $nameint = strripos($this->path, "/");
        $nameclean = substr($this->path, $nameint + 1);
        $pathclean = substr_replace($this->path, "", $nameint);
        $this->disk->putFileAs($pathclean, $file, $nameclean, 'public');
    }

    public function url($path) {
        return $this->disk->url($path);
    }

    public function makeDirectory() {
        
        $this->disk->makeDirectory($this->path, ...func_get_args());
        $this->disk->setVisibility($this->path, 'public');
    }

    public function extension() {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

}
