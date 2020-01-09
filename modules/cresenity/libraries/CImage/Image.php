<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Intervention\Image\ImageManagerStatic as InterventionImage;

/** @mixin CImage_Manipulations */
class CImage_Image {

    protected $pathToImage = '';

    /** @var string|null */
    protected $temporaryDirectory = null;

    /**
     * @param string $pathToImage
     *
     * @return static
     */
    public static function load($pathToImage) {
        return new static($pathToImage);
    }

    public function __construct($pathToImage) {
        if (!file_exists($pathToImage)) {
            throw new InvalidArgumentException("`{$pathToImage}` does not exist");
        }
        $this->pathToImage = $pathToImage;
        $this->manipulations = new CImage_Manipulations();
    }

    public function setTemporaryDirectory($tempDir) {
        $this->temporaryDirectory = $tempDir;
        return $this;
    }

    /**
     * @param string $imageDriver
     *
     * @return $this
     *
     * @throws InvalidImageDriver
     */
    public function useImageDriver($imageDriver) {
        if (!in_array($imageDriver, ['gd', 'imagick'])) {
            throw CImage_Exception_InvalidImageDriverException::driver($imageDriver);
        }
        $this->imageDriver = $imageDriver;
        InterventionImage::configure([
            'driver' => $this->imageDriver,
        ]);
        return $this;
    }

    public function mime() {
        return mime_content_type($this->pathToImage);
    }

    public function path() {
        return $this->pathToImage;
    }

    public function extension() {
        $extension = pathinfo($this->pathToImage, PATHINFO_EXTENSION);
        return strtolower($extension);
    }

    public function getWidth() {
        return InterventionImage::make($this->pathToImage)->width();
    }

    public function getHeight() {
        return InterventionImage::make($this->pathToImage)->height();
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
        if ($manipulations instanceof CImage_Manipulations) {
            $this->manipulations->mergeManipulations($manipulations);
        }
        return $this;
    }

    /**
     * 
     * @return CImage_ManipulationSequence
     */
    public function getManipulationSequence() {
        return $this->manipulations->getManipulationSequence();
    }

    public function save($outputPath = '') {
        if ($outputPath == '') {
            $outputPath = $this->pathToImage;
        }
        $this->addFormatManipulation($outputPath);
        $glideConversion = CImage_GlideConversion::create($this->pathToImage)->useImageDriver($this->imageDriver);
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
        $optimizerChain = CImage_OptimizerChainFactory::create();
        if (count($optimizerChainConfiguration)) {
            $optimizers = array_map(function (array $optimizerOptions, $optimizerClassName) {
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
        $supportedFormats = ['jpg', 'pjpg', 'png', 'gif', 'webp'];
        if (in_array($outputExtension, $supportedFormats)) {
            $this->manipulations->format($outputExtension);
        }
    }

    public function __call($name, $arguments) {
        if (!method_exists($this->manipulations, $name)) {
            throw new BadMethodCallException("Manipulation `{$name}` does not exist");
        }
        $this->manipulations->$name(...$arguments);
        return $this;
    }

}
