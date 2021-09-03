<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Aug 26, 2020
 */
class CRunner_FFMpeg_Media {
    /**
     * @var \ProtoneMedia\LaravelFFMpeg\Filesystem\Disk
     */
    private $disk;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \Spatie\TemporaryDirectory\TemporaryDirectory
     */
    private $temporaryDirectory;

    public function __construct(CRunner_FFMpeg_Storage_Disk $disk, $path) {
        $this->disk = $disk;
        $this->path = $path;

        $this->makeDirectory();
    }

    public static function make($disk, $path) {
        return new static(CRunner_FFMpeg_Storage_Disk::make($disk), $path);
    }

    public function getDisk() {
        return $this->disk;
    }

    public function getPath() {
        return $this->path;
    }

    public function getDirectory() {
        $directory = rtrim(pathinfo($this->getPath())['dirname'], DIRECTORY_SEPARATOR);

        if ($directory === '.') {
            $directory = '';
        }

        if ($directory) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        return $directory;
    }

    private function makeDirectory() {
        $directory = $this->getDirectory();

        $adapter = $this->getDisk()->isLocalDisk() ? $this->getDisk()->getFilesystemAdapter() : $this->temporaryDirectoryAdapter();

        if ($adapter->has($directory)) {
            return;
        }

        $adapter->makeDirectory($directory);
    }

    public function getFilenameWithoutExtension() {
        return pathinfo($this->getPath())['filename'];
    }

    public function getFilename() {
        return pathinfo($this->getPath())['basename'];
    }

    private function temporaryDirectoryAdapter() {
        if (!$this->temporaryDirectory) {
            $this->temporaryDirectory = $this->getDisk()->getTemporaryDirectory();
        }

        return CStorage::instance()->createLocalDriver(
            ['root' => $this->temporaryDirectory->path()]
        );
    }

    public function getLocalPath() {
        $disk = $this->getDisk();
        $path = $this->getPath();

        if ($disk->isLocalDisk()) {
            return $disk->path($path);
        }

        $temporaryDirectoryAdapter = $this->temporaryDirectoryAdapter();

        if ($disk->exists($path) && !$temporaryDirectoryAdapter->exists($path)) {
            $temporaryDirectoryAdapter->writeStream($path, $disk->readStream($path));
        }

        return $temporaryDirectoryAdapter->path($path);
    }

    public function copyAllFromTemporaryDirectory($visibility = null) {
        if (!$this->temporaryDirectory) {
            return $this;
        }

        $temporaryDirectoryAdapter = $this->temporaryDirectoryAdapter();

        $destinationAdapater = $this->getDisk()->getFilesystemAdapter();

        foreach ($temporaryDirectoryAdapter->allFiles() as $path) {
            $destinationAdapater->writeStream($path, $temporaryDirectoryAdapter->readStream($path));

            if ($visibility) {
                $destinationAdapater->setVisibility($path, $visibility);
            }
        }

        return $this;
    }

    public function setVisibility($visibility = null) {
        $disk = $this->getDisk();

        if ($visibility && $disk->isLocalDisk()) {
            $disk->setVisibility($visibility);
        }

        return $this;
    }
}
