<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 2:35:07 AM
 */

use League\Glide\Filesystem\FileNotFoundException;

class CImage_Manipulations {
    const CROP_TOP_LEFT = 'crop-top-left';

    const CROP_TOP = 'crop-top';

    const CROP_TOP_RIGHT = 'crop-top-right';

    const CROP_LEFT = 'crop-left';

    const CROP_CENTER = 'crop-center';

    const CROP_RIGHT = 'crop-right';

    const CROP_BOTTOM_LEFT = 'crop-bottom-left';

    const CROP_BOTTOM = 'crop-bottom';

    const CROP_BOTTOM_RIGHT = 'crop-bottom-right';

    const ORIENTATION_AUTO = 'auto';

    const ORIENTATION_90 = 90;

    const ORIENTATION_180 = 180;

    const ORIENTATION_270 = 270;

    const FLIP_HORIZONTALLY = 'h';

    const FLIP_VERTICALLY = 'v';

    const FLIP_BOTH = 'both';

    const FIT_CONTAIN = 'contain';

    const FIT_MAX = 'max';

    const FIT_FILL = 'fill';

    const FIT_STRETCH = 'stretch';

    const FIT_CROP = 'crop';

    const BORDER_OVERLAY = 'overlay';

    const BORDER_SHRINK = 'shrink';

    const BORDER_EXPAND = 'expand';

    const FORMAT_JPG = 'jpg';

    const FORMAT_PJPG = 'pjpg';

    const FORMAT_PNG = 'png';

    const FORMAT_GIF = 'gif';

    const FORMAT_WEBP = 'webp';

    const FILTER_GREYSCALE = 'greyscale';

    const FILTER_SEPIA = 'sepia';

    const UNIT_PIXELS = 'px';

    const UNIT_PERCENT = '%';

    const POSITION_TOP_LEFT = 'top-left';

    const POSITION_TOP = 'top';

    const POSITION_TOP_RIGHT = 'top-right';

    const POSITION_LEFT = 'left';

    const POSITION_CENTER = 'center';

    const POSITION_RIGHT = 'right';

    const POSITION_BOTTOM_LEFT = 'bottom-left';

    const POSITION_BOTTOM = 'bottom';

    const POSITION_BOTTOM_RIGHT = 'bottom-right';

    /**
     * @var CImage_ManipulationSequence
     */
    protected $manipulationSequence;

    public function __construct(array $manipulations = []) {
        if (!$this->hasMultipleConversions($manipulations)) {
            $manipulations = [$manipulations];
        }
        foreach ($manipulations as $manipulation) {
            $this->manipulationSequence = new CImage_ManipulationSequence($manipulation);
        }
    }

    /**
     * @param string $orientation
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function orientation($orientation) {
        if (!$this->validateManipulation($orientation, 'orientation')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'orientation',
                $orientation,
                $this->getValidManipulationOptions('orientation')
            );
        }

        return $this->addManipulation('orientation', $orientation);
    }

    /**
     * @param string $orientation
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function flip($orientation) {
        if (!$this->validateManipulation($orientation, 'flip')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'flip',
                $orientation,
                $this->getValidManipulationOptions('flip')
            );
        }

        return $this->addManipulation('flip', $orientation);
    }

    /**
     * @param string $cropMethod
     * @param int    $width
     * @param int    $height
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function crop($cropMethod, $width, $height) {
        if (!$this->validateManipulation($cropMethod, 'crop')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'cropmethod',
                $cropMethod,
                $this->getValidManipulationOptions('crop')
            );
        }
        $this->width($width);
        $this->height($height);

        return $this->addManipulation('crop', $cropMethod);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $focalX Crop center X in percent
     * @param int $focalY Crop center Y in percent
     *
     * @return $this
     */
    public function focalCrop($width, $height, $focalX, $focalY) {
        $this->width($width);
        $this->height($height);

        return $this->addManipulation('crop', "crop-{$focalX}-{$focalY}");
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function manualCrop($width, $height, $x, $y) {
        if ($width < 0) {
            throw CImage_Exception_InvalidManipulationException::invalidWidth($width);
        }
        if ($height < 0) {
            throw CImage_Exception_InvalidManipulationException::invalidHeight($height);
        }

        return $this->addManipulation('manualCrop', "{$width},{$height},{$x},{$y}");
    }

    /**
     * @param int $width
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function width($width) {
        if ($width < 0) {
            throw CImage_Exception_InvalidManipulationException::invalidWidth($width);
        }

        return $this->addManipulation('width', $width);
    }

    /**
     * @param int $height
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function height($height) {
        if ($height < 0) {
            throw CImage_Exception_InvalidManipulationException::invalidHeight($height);
        }

        return $this->addManipulation('height', $height);
    }

    /**
     * @param string $fitMethod
     * @param int    $width
     * @param int    $height
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function fit($fitMethod, $width, $height) {
        if (!$this->validateManipulation($fitMethod, 'fit')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'fit',
                $fitMethod,
                $this->getValidManipulationOptions('fit')
            );
        }
        $this->width($width);
        $this->height($height);

        return $this->addManipulation('fit', $fitMethod);
    }

    /**
     * @param int $ratio A value between 1 and 8
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function devicePixelRatio($ratio) {
        if ($ratio < 1 || $ratio > 8) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('ratio', $ratio, 1, 8);
        }

        return $this->addManipulation('devicePixelRatio', $ratio);
    }

    /**
     * @param int $brightness A value between -100 and 100
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function brightness($brightness) {
        if ($brightness < -100 || $brightness > 100) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('brightness', $brightness, -100, 100);
        }

        return $this->addManipulation('brightness', $brightness);
    }

    /**
     * @param float $gamma A value between 0.01 and 9.99
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function gamma($gamma) {
        if ($gamma < 0.01 || $gamma > 9.99) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('gamma', $gamma, 0.01, 9.00);
        }

        return $this->addManipulation('gamma', $gamma);
    }

    /**
     * @param int $contrast A value between -100 and 100
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function contrast($contrast) {
        if ($contrast < -100 || $contrast > 100) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('contrast', $contrast, -100, 100);
        }

        return $this->addManipulation('contrast', $contrast);
    }

    /**
     * @param int $sharpen A value between 0 and 100
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function sharpen($sharpen) {
        if ($sharpen < 0 || $sharpen > 100) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('sharpen', $sharpen, 0, 100);
        }

        return $this->addManipulation('sharpen', $sharpen);
    }

    /**
     * @param int $blur A value between 0 and 100
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function blur($blur) {
        if ($blur < 0 || $blur > 100) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('blur', $blur, 0, 100);
        }

        return $this->addManipulation('blur', $blur);
    }

    /**
     * @param int $pixelate A value between 0 and 1000
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function pixelate($pixelate) {
        if ($pixelate < 0 || $pixelate > 1000) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('pixelate', $pixelate, 0, 1000);
        }

        return $this->addManipulation('pixelate', $pixelate);
    }

    /**
     * @return $this
     */
    public function greyscale() {
        return $this->filter('greyscale');
    }

    /**
     * @return $this
     */
    public function sepia() {
        return $this->filter('sepia');
    }

    /**
     * @param string $colorName
     *
     * @return $this
     */
    public function background($colorName) {
        return $this->addManipulation('background', $colorName);
    }

    /**
     * @param int    $width
     * @param string $color
     * @param string $borderType
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function border($width, $color, $borderType = 'overlay') {
        if ($width < 0) {
            throw CImage_Exception_InvalidManipulationException::invalidWidth($width);
        }
        if (!$this->validateManipulation($borderType, 'border')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'border',
                $borderType,
                $this->getValidManipulationOptions('border')
            );
        }

        return $this->addManipulation('border', "{$width},{$color},{$borderType}");
    }

    /**
     * @param int $quality
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function quality($quality) {
        if ($quality < 0 || $quality > 100) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('quality', $quality, 0, 100);
        }

        return $this->addManipulation('quality', $quality);
    }

    /**
     * @param string $format
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function format($format) {
        if (!$this->validateManipulation($format, 'format')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'format',
                $format,
                $this->getValidManipulationOptions('format')
            );
        }

        return $this->addManipulation('format', $format);
    }

    /**
     * @param string $filterName
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    protected function filter($filterName) {
        if (!$this->validateManipulation($filterName, 'filter')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'filter',
                $filterName,
                $this->getValidManipulationOptions('filter')
            );
        }

        return $this->addManipulation('filter', $filterName);
    }

    /**
     * @param string $filePath
     *
     * @throws FileNotFoundException
     *
     * @return $this
     */
    public function watermark($filePath) {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException($filePath);
        }
        $this->addManipulation('watermark', $filePath);

        return $this;
    }

    /**
     * @param int    $width the width of the watermark in pixels (default) or percent
     * @param string $unit  The unit of the `$width` parameter. Use `Manipulations::UNIT_PERCENT` or `Manipulations::UNIT_PIXELS`.
     *
     * @return $this
     */
    public function watermarkWidth($width, $unit = 'px') {
        $width = ($unit == static::UNIT_PERCENT ? $width . 'w' : $width);

        return $this->addManipulation('watermarkWidth', $width);
    }

    /**
     * @param int    $height the height of the watermark in pixels (default) or percent
     * @param string $unit   The unit of the `$height` parameter. Use `Manipulations::UNIT_PERCENT` or `Manipulations::UNIT_PIXELS`.
     *
     * @return $this
     */
    public function watermarkHeight($height, $unit = 'px') {
        $height = ($unit == static::UNIT_PERCENT ? $height . 'h' : $height);

        return $this->addManipulation('watermarkHeight', $height);
    }

    /**
     * @param string $fitMethod how is the watermark fitted into the watermarkWidth and watermarkHeight properties
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function watermarkFit($fitMethod) {
        if (!$this->validateManipulation($fitMethod, 'fit')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'watermarkFit',
                $fitMethod,
                $this->getValidManipulationOptions('fit')
            );
        }

        return $this->addManipulation('watermarkFit', $fitMethod);
    }

    /**
     * @param int      $xPadding how far is the watermark placed from the left and right edges of the image
     * @param null|int $yPadding how far is the watermark placed from the top and bottom edges of the image
     * @param string   $unit     Unit of the padding values. Use `Manipulations::UNIT_PERCENT` or `Manipulations::UNIT_PIXELS`.
     *
     * @return $this
     */
    public function watermarkPadding($xPadding, $yPadding = null, $unit = 'px') {
        $yPadding = $yPadding ?: $xPadding;
        $xPadding = ($unit == static::UNIT_PERCENT ? $xPadding . 'w' : $xPadding);
        $yPadding = ($unit == static::UNIT_PERCENT ? $yPadding . 'h' : $yPadding);
        $this->addManipulation('watermarkPaddingX', $xPadding);
        $this->addManipulation('watermarkPaddingY', $yPadding);

        return $this;
    }

    /**
     * @param string $position
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function watermarkPosition($position) {
        if (!$this->validateManipulation($position, 'position')) {
            throw CImage_Exception_InvalidManipulationException::invalidParameter(
                'watermarkPosition',
                $position,
                $this->getValidManipulationOptions('position')
            );
        }

        return $this->addManipulation('watermarkPosition', $position);
    }

    /**
     * Sets the opacity of the watermark. Only works with the `imagick` driver.
     *
     * @param int $opacity a value between 0 and 100
     *
     * @throws CImage_Exception_InvalidManipulationException
     *
     * @return $this
     */
    public function watermarkOpacity($opacity) {
        if ($opacity < 0 || $opacity > 100) {
            throw CImage_Exception_InvalidManipulationException::valueNotInRange('opacity', $opacity, 0, 100);
        }

        return $this->addManipulation('watermarkOpacity', $opacity);
    }

    /**
     * Shave off some kilobytes by optimizing the image.
     *
     * @param array $optimizationOptions
     *
     * @return $this
     */
    public function optimize(array $optimizationOptions = []) {
        return $this->addManipulation('optimize', json_encode($optimizationOptions));
    }

    /**
     * @return $this
     */
    public function apply() {
        $this->manipulationSequence->startNewGroup();

        return $this;
    }

    /**
     * Create new manipulations class.
     *
     * @param array $manipulations
     *
     * @return self
     */
    public static function create(array $manipulations = []) {
        return new self($manipulations);
    }

    public function toArray() {
        return $this->manipulationSequence->toArray();
    }

    /**
     * Checks if the given manipulations has arrays inside or not.
     *
     * @param array $manipulations
     *
     * @return bool
     */
    private function hasMultipleConversions(array $manipulations) {
        foreach ($manipulations as $manipulation) {
            if (isset($manipulation[0]) && is_array($manipulation[0])) {
                return true;
            }
        }

        return false;
    }

    public function removeManipulation($name) {
        $this->manipulationSequence->removeManipulation($name);
    }

    public function hasManipulation($manipulationName) {
        return !is_null($this->getManipulationArgument($manipulationName));
    }

    /**
     * @param string $manipulationName
     *
     * @return null|string
     */
    public function getManipulationArgument($manipulationName) {
        foreach ($this->manipulationSequence->getGroups() as $manipulationSet) {
            if (array_key_exists($manipulationName, $manipulationSet)) {
                return $manipulationSet[$manipulationName];
            }
        }
    }

    protected function addManipulation($manipulationName, $manipulationArgument) {
        $this->manipulationSequence->addManipulation($manipulationName, $manipulationArgument);

        return $this;
    }

    public function mergeManipulations(CImage_Manipulations $manipulations) {
        $this->manipulationSequence->merge($manipulations->manipulationSequence);

        return $this;
    }

    public function getManipulationSequence() {
        return $this->manipulationSequence;
    }

    protected function validateManipulation($value, $constantNamePrefix) {
        return in_array($value, $this->getValidManipulationOptions($constantNamePrefix));
    }

    protected function getValidManipulationOptions($manipulation) {
        $options = (new ReflectionClass(static::class))->getConstants();

        return array_filter($options, function ($value, $name) use ($manipulation) {
            return strpos($name, strtoupper($manipulation)) === 0;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function isEmpty() {
        return $this->manipulationSequence->isEmpty();
    }

    /**
     * Get the first manipultion with the given name.
     *
     * @param mixed $manipulationName
     *
     * @return mixed
     */
    public function getFirstManipulationArgument($manipulationName) {
        return $this->manipulationSequence->getFirstManipulationArgument($manipulationName);
    }
}
