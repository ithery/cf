<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 11:50:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCDN_Asset implements CCDN_AssetInterface {

    /**
     * default [include] configurations.
     *
     * @var array
     */
    protected $defaultInclude = [
        'directories' => ['public'],
        'extensions' => [],
        'patterns' => [],
    ];

    /**
     * default [exclude] configurations.
     *
     * @var array
     */
    protected $defaultExclude = [
        'directories' => [],
        'files' => [],
        'extensions' => [],
        'patterns' => [],
        'hidden' => true,
    ];

    /**
     * @var array
     */
    protected $includedDirectories;

    /**
     * @var array
     */
    protected $includedFiles;

    /**
     * @var array
     */
    protected $includedExtensions;

    /**
     * @var array
     */
    protected $includedPatterns;

    /**
     * @var array
     */
    protected $excludedDirectories;

    /**
     * @var array
     */
    protected $excludedFiles;

    /**
     * @var array
     */
    protected $excludedExtensions;

    /**
     * @var array
     */
    protected $excludedPatterns;
    /*
     * @var boolean
     */
    protected $excludeHidden;
    /*
     * Allowed assets for upload (found in includedDirectories)
     *
     * @var Collection
     */
    public $assets;

    /**
     * build a Asset object that contains the assets related configurations.
     *
     * @param array $configurations
     *
     * @return $this
     */
    public function init($configurations = []) {
        $this->parseAndFillConfiguration($configurations);
        $this->includedDirectories = $this->defaultInclude['directories'];
        $this->includedExtensions = $this->defaultInclude['extensions'];
        $this->includedPatterns = $this->defaultInclude['patterns'];
        $this->excludedDirectories = $this->defaultExclude['directories'];
        $this->excludedFiles = $this->defaultExclude['files'];
        $this->excludedExtensions = $this->defaultExclude['extensions'];
        $this->excludedPatterns = $this->defaultExclude['patterns'];
        $this->excludeHidden = $this->defaultExclude['hidden'];
        return $this;
    }

    /**
     * Check if the config file has any missed attribute, and if any attribute
     * is missed will be overridden by a default attribute defined in this class.
     *
     * @param $configurations
     */
    private function parseAndFillConfiguration($configurations) {
        $this->defaultInclude = isset($configurations['include']) ?
                array_merge($this->defaultInclude, $configurations['include']) : $this->defaultInclude;
        $this->defaultExclude = isset($configurations['exclude']) ?
                array_merge($this->defaultExclude, $configurations['exclude']) : $this->defaultExclude;
    }

    /**
     * @return array
     */
    public function getIncludedDirectories() {
        return $this->includedDirectories;
    }

    /**
     * @return array
     */
    public function getIncludedExtensions() {
        return $this->includedExtensions;
    }

    /**
     * @return array
     */
    public function getIncludedPatterns() {
        return $this->includedPatterns;
    }

    /**
     * @return array
     */
    public function getExcludedDirectories() {
        return $this->excludedDirectories;
    }

    /**
     * @return array
     */
    public function getExcludedFiles() {
        return $this->excludedFiles;
    }

    /**
     * @return array
     */
    public function getExcludedExtensions() {
        return $this->excludedExtensions;
    }

    /**
     * @return array
     */
    public function getExcludedPatterns() {
        return $this->excludedPatterns;
    }

    /**
     * @return Collection
     */
    public function getAssets() {
        return $this->assets;
    }

    /**
     * @param mixed $assets
     */
    public function setAssets($assets) {
        $this->assets = $assets;
    }

    /**
     * @return mixed
     */
    public function getExcludeHidden() {
        return $this->excludeHidden;
    }

}
