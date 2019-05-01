<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:34:07 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_File {

    /** @var string */
    protected $pathToImage;

    /** @var \Spatie\Image\Manipulations */
    protected $manipulations;
    protected $imageDriver = 'gd';

    /** @var string|null */
    protected $temporaryDirectory = null;

    /**
     * @param string $pathToImage
     *
     * @return static
     */
    public static function load(string $pathToImage) {
        return new static($pathToImage);
    }

    public function setTemporaryDirectory($tempDir) {
        $this->temporaryDirectory = $tempDir;
        return $this;
    }

    public function __construct(string $pathToImage) {
        $this->pathToImage = $pathToImage;
        $this->manipulations = new Manipulations();
    }

    /**
     * @param string $imageDriver
     *
     * @return $this
     *
     * @throws InvalidImageDriver
     */
    public function useImageDriver(string $imageDriver) {
        if (!in_array($imageDriver, ['gd', 'imagick'])) {
            throw InvalidImageDriver::driver($imageDriver);
        }
        $this->imageDriver = $imageDriver;
        InterventionImage::configure([
            'driver' => $this->imageDriver,
        ]);
        return $this;
    }

    /**
     * @param callable|$manipulations
     *
     * @return $this
     */
    public function manipulate($manipulations) {
        if (is_callable($manipulations)) {
            $manipulations($this->manipulations);
        }
        if ($manipulations instanceof Manipulations) {
            $this->manipulations->mergeManipulations($manipulations);
        }
        return $this;
    }

    public function __call($name, $arguments) {
        if (!method_exists($this->manipulations, $name)) {
            throw new BadMethodCallException("Manipulation `{$name}` does not exist");
        }
        $this->manipulations->$name(...$arguments);
        return $this;
    }

    public function getWidth() {
        return InterventionImage::make($this->pathToImage)->width();
    }

    public function getHeight() {
        return InterventionImage::make($this->pathToImage)->height();
    }

    public function getManipulationSequence() {
        return $this->manipulations->getManipulationSequence();
    }

    public function save($outputPath = '') {
        if ($outputPath == '') {
            $outputPath = $this->pathToImage;
        }
        $this->addFormatManipulation($outputPath);
        $glideConversion = GlideConversion::create($this->pathToImage)->useImageDriver($this->imageDriver);
        if (!is_null($this->temporaryDirectory)) {
            $glideConversion->setTemporaryDirectory($this->temporaryDirectory);
        }
        $glideConversion->performManipulations($this->manipulations);
        $glideConversion->save($outputPath);
        if ($this->shouldOptimize()) {
            $optimizerChainConfiguration = $this->manipulations->getFirstManipulationArgument('optimize');
            $optimizerChainConfiguration = json_decode($optimizerChainConfiguration, true);
            $this->performOptimization($outputPath, $optimizerChainConfiguration);
        }
    }

    protected function shouldOptimize() {
        return !is_null($this->manipulations->getFirstManipulationArgument('optimize'));
    }

    protected function performOptimization($path, array $optimizerChainConfiguration) {
        $optimizerChain = OptimizerChainFactory::create();
        if (count($optimizerChainConfiguration)) {
            $optimizers = array_map(function (array $optimizerOptions,  $optimizerClassName) {
                return (new $optimizerClassName)->setOptions($optimizerOptions);
            }, $optimizerChainConfiguration, array_keys($optimizerChainConfiguration));
            $optimizerChain->setOptimizers($optimizers);
        }
        $optimizerChain->optimize($path);
    }

    protected function addFormatManipulation($outputPath) {
        if ($this->manipulations->hasManipulation('format')) {
            return;
        }
        $inputExtension = strtolower(pathinfo($this->pathToImage, PATHINFO_EXTENSION));
        $outputExtension = strtolower(pathinfo($outputPath, PATHINFO_EXTENSION));
        if ($inputExtension === $outputExtension) {
            return;
        }
        $supportedFormats = ['jpg', 'png', 'gif'];
        if (in_array($outputExtension, $supportedFormats)) {
            $this->manipulations->format($outputExtension);
        }
    }

}
