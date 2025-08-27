<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 2:47:36 AM
 */
class CResources_ResourceCollection {
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $diskName = '';

    /**
     * @var string
     */
    public $conversionsDiskName = '';

    /**
     * @var callable
     */
    public $resourceConversionRegistrations;

    /**
     * @var bool
     */
    public $generateResponsiveImages = false;

    /**
     * @var callable
     */
    public $acceptsFile;

    /**
     * @var array
     */
    public $acceptsMimeTypes = [];

    /**
     * @var int
     */
    public $collectionSizeLimit = false;

    /**
     * @var bool
     */
    public $singleFile = false;

    /**
     * @var string
     */
    public $fallbackUrls = [];

    /**
     * @var string
     */
    public $fallbackPaths = [];

    public function __construct($name) {
        $this->name = $name;
        $this->resourceConversionRegistrations = function () {
        };
        $this->acceptsFile = function () {
            return true;
        };
    }

    public static function create($name) {
        return new static($name);
    }

    public function useDisk($diskName) {
        $this->diskName = $diskName;

        return $this;
    }

    public function storeConversionsOnDisk($conversionsDiskName) {
        $this->conversionsDiskName = $conversionsDiskName;

        return $this;
    }

    public function acceptsFile(callable $acceptsFile) {
        $this->acceptsFile = $acceptsFile;

        return $this;
    }

    public function acceptsMimeTypes($mimeTypes) {
        $this->acceptsMimeTypes = $mimeTypes;

        return $this;
    }

    /**
     * @return CResources_ResourceCollection
     */
    public function singleFile() {
        return $this->onlyKeepLatest(1);
    }

    /**
     * @param int $maximumNumberOfItemsInCollection
     *
     * @return CResources_ResourceCollection
     */
    public function onlyKeepLatest($maximumNumberOfItemsInCollection) {
        if ($maximumNumberOfItemsInCollection < 1) {
            throw new InvalidArgumentException("You should pass a value higher than 0. `{$maximumNumberOfItemsInCollection}` given.");
        }
        $this->singleFile = ($maximumNumberOfItemsInCollection === 1);
        $this->collectionSizeLimit = $maximumNumberOfItemsInCollection;

        return $this;
    }

    public function registerResourceConversions(callable $resourceConversionRegistrations) {
        $this->resourceConversionRegistrations = $resourceConversionRegistrations;
    }

    public function useFallbackUrl($url, string $conversionName = '') {
        if ($conversionName === '') {
            $conversionName = 'default';
        }

        $this->fallbackUrls[$conversionName] = $url;

        return $this;
    }

    public function useFallbackPath($path, string $conversionName = '') {
        if ($conversionName === '') {
            $conversionName = 'default';
        }

        $this->fallbackPaths[$conversionName] = $path;

        return $this;
    }

    public function withResponsiveImages() {
        $this->generateResponsiveImages = true;

        return $this;
    }

    public function withResponsiveImagesIf($condition) {
        $this->generateResponsiveImages = (bool) (is_callable($condition) ? $condition() : $condition);

        return $this;
    }
}
