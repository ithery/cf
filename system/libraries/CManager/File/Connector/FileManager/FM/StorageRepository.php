<?php

defined('SYSPATH') or die('No direct access allowed.');

use League\Flysystem\Cached\CachedAdapter;

class CManager_File_Connector_FileManager_FM_StorageRepository {
    /**
     * @var CStorage_Adapter
     */
    private $disk;

    /**
     * Storage-relative path (e.g. 'files/{user_slug}') this repository operates on.
     *
     * @var string
     */
    private $path;

    /**
     * @var CManager_File_Connector_FileManager_FM
     */
    private $helper;

    /**
     * @param string                                  $storage_path
     * @param CManager_File_Connector_FileManager_FM  $helper
     *
     * @return void
     */
    public function __construct($storage_path, CManager_File_Connector_FileManager_FM $helper) {
        $this->helper = $helper;
        $this->disk = CStorage::instance()->disk($this->helper->config('disk'));
        $this->path = $storage_path;
    }

    /**
     * Proxies any undefined method call through to the underlying disk, passing
     * this repository's own $path as the disk method's first argument.
     *
     * @param string $functionName
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($functionName, $arguments) {
        // TODO: remove __call, define all function which storage must support
        return $this->disk->$functionName($this->path, ...$arguments);
    }

    /**
     * @return bool
     */
    public function exists() {
        return $this->disk->exists($this->path);
    }

    /**
     * @return string
     */
    public function rootPath() {
        $prefixer = $this->disk->getPrefixer();

        return $prefixer->prefixPath('');
        $adapter = $this->disk->getDriver()->getAdapter();
        if ($adapter instanceof CachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        $pathPrefix = $adapter->getPathPrefix();

        return $pathPrefix;
    }

    /**
     * @param CManager_File_Connector_FileManager_FM_Path $newFmPath
     *
     * @return mixed
     */
    public function move($newFmPath) {
        if ($this->isDirectory()) {
            return $this->moveRecursive($this->path, $newFmPath->path('storage'));
        }

        return $this->disk->move($this->path, $newFmPath->path('storage'));
    }

    /**
     * @param null|string $path defaults to this repository's own $path
     *
     * @return bool
     */
    public function isDirectory($path = null) {
        if ($path == null) {
            $path = $this->path;
        }
        $path = rtrim($path, '/');
        if (strlen($path) == 0) {
            return false;
        }
        $pathExploded = explode('/', $path);
        $lastDirectory = carr::get($pathExploded, count($pathExploded) - 1);
        $parentPath = substr($path, 0, strlen($path) - strlen($lastDirectory));

        return in_array($path, $this->disk->directories($parentPath));
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    protected function moveRecursive($from, $to) {
        if ($this->isDirectory($from)) {
            return $this->moveDirectory($from, $to);
        }

        return $this->disk->move($from, $to);
    }

    /**
     * @param string $from
     * @param string $to
     *
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

    /**
     * @param mixed $file
     *
     * @return void
     */
    public function save($file) {
        $nameint = strripos($this->path, '/');
        $nameclean = substr($this->path, $nameint + 1);
        $pathclean = substr_replace($this->path, '', $nameint);
        $this->disk->putFileAs($pathclean, $file, $nameclean, 'public');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function url($path) {
        return $this->disk->url($path);
    }

    /**
     * Creates this repository's own $path as a directory on the disk (visibility
     * forced to public). Takes variadic arguments passed straight through to the
     * disk's own makeDirectory() (e.g. mode, recursive, force).
     *
     * @return void
     */
    public function makeDirectory() {
        $this->disk->makeDirectory($this->path, ...func_get_args());
        $this->disk->setVisibility($this->path, 'public');
    }

    /**
     * @param null|string $path
     *
     * @return void
     */
    protected function createDirectory($path = null) {
        // CStorage_Adapter::makeDirectory() only takes $path -- Flysystem's own
        // createDirectory() already handles recursion, and visibility is set
        // explicitly below rather than via a mode argument.
        $this->disk->makeDirectory($path);
        $this->disk->setVisibility($path, 'public');
    }

    /**
     * @return string
     */
    public function extension() {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * @return CStorage_Adapter
     */
    public function disk() {
        return $this->disk;
    }
}
