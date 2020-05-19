<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 3:25:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use League\Flysystem\Cached\CachedAdapter;

class CManager_File_Connector_FileManager_FM_StorageRepository {

    /**
     *
     * @var CStorage_Adapter
     */
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


        $pathPrefix = $adapter->getPathPrefix();


        return $pathPrefix;
    }

    public function move($newFmPath) {
        if ($this->isDirectory()) {

            return $this->moveRecursive($this->path, $newFmPath->path('storage'));
        }
        return $this->disk->move($this->path, $newFmPath->path('storage'));
    }

    public function isDirectory($path = null) {
        if ($path == null) {
            $path = $this->path;
        }
        $path = rtrim($path, '/');
        if (strlen($path) == 0) {
            return false;
        }
        $pathExploded = explode("/", $path);
        $lastDirectory = carr::get($pathExploded, count($pathExploded) - 1);
        $parentPath = substr($path, 0, strlen($path) - strlen($lastDirectory));
        return in_array($path, $this->disk->directories($parentPath));
    }

    protected function moveRecursive($from, $to) {
        if ($this->isDirectory($from)) {
            return $this->moveDirectory($from, $to);
        }
        return $this->disk->move($from, $to);
    }

    /**
     * @param string $from
     * @param string $to
     * @return bool
     */
    protected function moveDirectory($from, $to) {

        if ($this->disk->has($from)) {
            $folderContents = $this->disk->listContents($from, true);
            foreach ($folderContents as $content) {
                if ($content['type'] === 'file') {
                    $src = $content['path'];
                    $dest = str_replace($from, $to, $content['path']);
                    $this->moveRecursive($src, $dest);
                }
            }
            if (!$this->disk->exists($to)) {
                
                $this->createDirectory($to);
            }
            $this->disk->deleteDirectory($from);
        }
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

    protected function createDirectory($path = null) {
        $this->disk->makeDirectory($path, 755, true, true);
        $this->disk->setVisibility($path, 'public');
    }

    public function extension() {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

}
