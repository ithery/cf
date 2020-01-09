<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:47:36 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_ResourceCollection {

    /** @var string */
    public $name = '';

    /** @var string */
    public $diskName = '';

    /** @var callable */
    public $resourceConversionRegistrations;

    /** @var callable */
    public $acceptsFile;

    /** @var array $acceptsMimeTypes */
    public $acceptsMimeTypes = [];

    /** @var int */
    public $collectionSizeLimit = false;

    /** @var bool */
    public $singleFile = false;

    /** @var string */
    public $fallbackUrl = '';

    /** @var string */
    public $fallbackPath = '';

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

    public function acceptsFile(callable $acceptsFile) {
        $this->acceptsFile = $acceptsFile;
        return $this;
    }

    public function acceptsMimeTypes($mimeTypes) {
        $this->acceptsMimeTypes = $mimeTypes;
        return $this;
    }

    public function singleFile() {
        return $this->onlyKeepLatest(1);
    }

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

    public function useFallbackUrl($url) {
        $this->fallbackUrl = $url;
        return $this;
    }

    public function useFallbackPath($path) {
        $this->fallbackPath = $path;
        return $this;
    }

}
