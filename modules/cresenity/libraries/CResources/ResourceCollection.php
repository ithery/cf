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

    /** @var bool */
    public $singleFile = false;

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

    public function singleFile() {
        $this->singleFile = true;
        return $this;
    }

    public function registerResourceConversions(callable $resourceConversionRegistrations) {
        $this->resourceConversionRegistrations = $resourceConversionRegistrations;
    }

}
