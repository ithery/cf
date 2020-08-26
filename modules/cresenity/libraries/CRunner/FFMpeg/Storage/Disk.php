<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem as LeagueFilesystem;
use Spatie\TemporaryDirectory\TemporaryDirectory;

/**
 * @mixin CStorage_Adapter
 */
class CRunner_FFMpeg_Storage_Disk {

    use CTrait_ForwardsCalls;

    /**
     * @var string|\Illuminate\Contracts\Filesystem\Filesystem
     */
    private $disk;

    /**
     * @var \Spatie\TemporaryDirectory\TemporaryDirectory
     */
    private $temporaryDirectory;

    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    private $filesystemAdapter;

    public function __construct($disk) {
        $this->disk = $disk;
    }

    /**
     * Little helper method to instantiate this class.
     */
    public static function make($disk) {
        if ($disk instanceof self) {
            return $disk;
        }

        return new static($disk);
    }

    /**
     * Creates a fresh instance, mostly used to force a new TemporaryDirectory.
     */
    public function doClone() {
        return new static($this->disk);
    }

    /**
     * Creates a new TemporaryDirectory instance if none is set, otherwise
     * it returns the current one.
     */
    public function getTemporaryDirectory() {
        if ($this->temporaryDirectory) {
            return $this->temporaryDirectory;
        }

        return $this->temporaryDirectory = TemporaryDirectories::create();
    }

    public function makeMedia($path) {
        return CRunner_FFMpeg_Media::make($this, $path);
    }

    /**
     * Returns the name of the disk. It generates a name if the disk
     * is an instance of Flysystem.
     */
    public function getName() {
        if (is_string($this->disk)) {
            return $this->disk;
        }

        return get_class($this->getFlysystemAdapter()) . "_" . md5(json_encode(serialize($this->getFlysystemAdapter())));
    }

    public function getFilesystemAdapter() {
        if ($this->filesystemAdapter) {
            return $this->filesystemAdapter;
        }

        if ($this->disk instanceof Filesystem) {
            return $this->filesystemAdapter = $this->disk;
        }

        return $this->filesystemAdapter = CStorage::instance()->disk($this->disk);
    }

    private function getFlysystemDriver() {
        return $this->getFilesystemAdapter()->getDriver();
    }

    private function getFlysystemAdapter() {
        return $this->getFlysystemDriver()->getAdapter();
    }

    public function isLocalDisk() {
        return $this->getFlysystemAdapter() instanceof Local;
    }

    /**
     * Forwards all calls to Laravel's FilesystemAdapter which will pass
     * dynamic methods call onto Flysystem.
     */
    public function __call($method, $parameters) {
        return $this->forwardCallTo($this->getFilesystemAdapter(), $method, $parameters);
    }

}
