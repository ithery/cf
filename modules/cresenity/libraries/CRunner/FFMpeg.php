<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Media\AbstractMediaType;

/**
 * @mixin \ProtoneMedia\LaravelFFMpeg\Drivers\PHPFFMpeg
 */
class CRunner_FFMpeg {

    use CTrait_ForwardsCalls;

    /**
     * @var CRunner_FFMpeg_Storage_Disk
     */
    private $disk;

    /**
     * @var \ProtoneMedia\LaravelFFMpeg\Drivers\PHPFFMpeg
     */
    private $driver;

    /**
     * @var \ProtoneMedia\LaravelFFMpeg\Filesystem\MediaCollection
     */
    private $collection;

    /**
     * @var \FFMpeg\Coordinate\TimeCode
     */
    private $timecode;

    /**
     * Uses the 'filesystems.default' disk from the config if none is given.
     * Gets the underlying PHPFFMpeg instance from the container if none is given.
     * Instantiates a fresh MediaCollection if none is given.
     */
    public function __construct($disk = null, CRunner_FFMpeg_Driver_PHPFFMpeg $driver = null, CRunner_FFMpeg_MediaCollection $mediaCollection = null) {
        $this->disk =CRunner_FFMpeg_Storage_Disk::make($disk ?: CF::config('storage.default'));

        $this->driver = $driver ?: new CRunner_FFMpeg_Driver_PHPFFMpeg();

        $this->collection = $mediaCollection ?: new CRunner_FFMpeg_MediaCollection;
    }

    public function doClone() {
        return new static(
                $this->disk,
                $this->driver,
                $this->collection
        );
    }

    /**
     * Set the disk to open files from.
     */
    public function fromDisk($disk) {
        $this->disk = CRunner_FFMpeg_Storage_Disk::make($disk);

        return $this;
    }

    /**
     * Instantiates a Media object for each given path.
     */
    public function open($path) {
        $paths = carr::wrap($path);

        foreach ($paths as $path) {
            $this->collection->push(CRunner_FFMpeg_Media::make($this->disk, $path));
        }

        return $this;
    }

    public function get() {
        return $this->collection;
    }

    public function getDriver() {
        return $this->driver->open($this->collection);
    }

    /**
     * Forces the driver to open the collection with the `openAdvanced` method.
     */
    public function getAdvancedDriver() {
        return $this->driver->openAdvanced($this->collection);
    }

    /**
     * Shortcut to set the timecode by string.
     */
    public function getFrameFromString($timecode) {
        return $this->getFrameFromTimecode(TimeCode::fromString($timecode));
    }

    /**
     * Shortcut to set the timecode by seconds.
     */
    public function getFrameFromSeconds($quantity) {
        return $this->getFrameFromTimecode(TimeCode::fromSeconds($quantity));
    }

    public function getFrameFromTimecode(TimeCode $timecode) {
        $this->timecode = $timecode;

        return $this;
    }

    /**
     * Returns an instance of MediaExporter with the driver and timecode (if set).
     */
    public function export() {
        return c::tap(new CRunner_FFMpeg_Exporter_MediaExporter($this->getDriver()), function (CRunner_FFMpeg_Exporter_MediaExporter $mediaExporter) {
                    if ($this->timecode) {
                        $mediaExporter->frame($this->timecode);
                    }
                });
    }

    /**
     * Returns an instance of HLSExporter with the driver forced to AdvancedMedia.
     */
    public function exportForHLS() {
        return new CRunner_FFMpeg_Exporter_HLSExporter($this->getAdvancedDriver());
    }

    public function cleanupTemporaryFiles() {
        CRunner_FFMpeg_Storage_TemporaryDirectories::deleteAll();

        return $this;
    }

    public function each($items, callable $callback) {
        CCollection::make($items)->each(function ($item, $key) use ($callback) {
            return $callback($this->doClone(), $item, $key);
        });

        return $this;
    }

    /**
     * Returns the Media object from the driver.
     */
    public function __invoke() {
        return $this->getDriver()->get();
    }

    /**
     * Forwards all calls to the underlying driver.
     * @return void
     */
    public function __call($method, $arguments) {
        $result = $this->forwardCallTo($driver = $this->getDriver(), $method, $arguments);

        return ($result === $driver) ? $this : $result;
    }

}
