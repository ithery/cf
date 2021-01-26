<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Aug 26, 2020
 */
use FFMpeg\Format\FormatInterface;

/**
 * @mixin \CRunner_FFMpeg_Driver_PHPFFMpeg
 */
class CRunner_FFMpeg_Exporter_MediaExporter {
    use CTrait_ForwardsCalls,
        CRunner_FFMpeg_Trait_HandlesAdvancedMediaTrait,
        CRunner_FFMpeg_Trait_HandlesConcatenationTrait,
        CRunner_FFMpeg_Trait_HandlesFramesTrait,
        CRunner_FFMpeg_Trait_HandlesTimelapseTrait,
        CRunner_FFMpeg_Trait_HasProgressListenerTrait;

    /**
     * @var \CRunner_FFMpeg_Driver_PHPFFMpeg
     */
    protected $driver;

    /**
     * @var \FFMpeg\Format\FormatInterface
     */
    private $format;

    /**
     * @var string
     */
    protected $visibility;

    /**
     * @var \CRunner_FFMpeg_Storage_Disk
     */
    private $toDisk;

    public function __construct(CRunner_FFMpeg_Driver_PHPFFMpeg $driver) {
        $this->driver = $driver;

        $this->maps = new CCollection;
    }

    protected function getDisk() {
        if ($this->toDisk) {
            return $this->toDisk;
        }

        $media = $this->driver->getMediaCollection();

        return $this->toDisk = $media->first()->getDisk();
    }

    public function inFormat(FormatInterface $format) {
        $this->format = $format;

        return $this;
    }

    public function toDisk($disk) {
        $this->toDisk = CRunner_FFMpeg_Storage_Disk::make($disk);

        return $this;
    }

    public function withVisibility($visibility) {
        $this->visibility = $visibility;

        return $this;
    }

    public function getCommand($path = null) {
        $this->driver->getPendingComplexFilters()->each->apply($this->driver, $this->maps);

        $this->maps->each->apply($this->driver->get());

        return $this->driver->getFinalCommand(
            $this->format,
            $path ? $this->getDisk()->makeMedia($path)->getLocalPath() : null
        );
    }

    public function save($path = null) {
        $outputMedia = $path ? $this->getDisk()->makeMedia($path) : null;

        if ($this->concatWithTranscoding && $outputMedia) {
            $this->addConcatFilterAndMapping($outputMedia);
        }

        if ($this->maps->isNotEmpty()) {
            return $this->saveWithMappings();
        }

        if ($this->format && $this->onProgressCallback) {
            $this->applyProgressListenerToFormat($this->format);
        }

        if ($this->timelapseFramerate > 0) {
            $this->addTimelapseParametersToFormat();
        }

        if ($this->driver->isConcat() && $outputMedia) {
            $this->driver->saveFromSameCodecs($outputMedia->getLocalPath());
        } elseif ($this->driver->isFrame()) {
            $data = $this->driver->save(
                c::optional($outputMedia)->getLocalPath(),
                $this->getAccuracy(),
                $this->returnFrameContents
            );

            if ($this->returnFrameContents) {
                return $data;
            }
        } else {
            $this->driver->save($this->format, $outputMedia->getLocalPath());
        }

        $outputMedia->copyAllFromTemporaryDirectory($this->visibility);
        $outputMedia->setVisibility($this->visibility);

        if ($this->onProgressCallback) {
            call_user_func($this->onProgressCallback, 100, 0, 0);
        }

        return $this->getMediaOpener();
    }

    private function saveWithMappings() {
        $this->driver->getPendingComplexFilters()->each->apply($this->driver, $this->maps);

        $this->maps->map->apply($this->driver->get());

        if ($this->onProgressCallback) {
            $this->applyProgressListenerToFormat($this->maps->last()->getFormat());
        }

        $this->driver->save();

        if ($this->onProgressCallback) {
            call_user_func($this->onProgressCallback, 100);
        }

        $this->maps->map->getOutputMedia()->each->copyAllFromTemporaryDirectory($this->visibility);

        return $this->getMediaOpener();
    }

    protected function getMediaOpener() {
        return new CRunner_FFMpeg(
            $this->driver->getMediaCollection()->last()->getDisk(),
            $this->driver,
            $this->driver->getMediaCollection()
        );
    }

    /**
     * Forwards the call to the driver object and returns the result
     * if it's something different than the driver object itself.
     *
     * @param mixed $method
     * @param mixed $arguments
     */
    public function __call($method, $arguments) {
        $result = $this->forwardCallTo($driver = $this->driver, $method, $arguments);

        return ($result === $driver) ? $this : $result;
    }
}
