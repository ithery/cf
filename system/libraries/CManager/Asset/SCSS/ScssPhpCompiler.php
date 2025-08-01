<?php

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\Exception\SassException;

class CManager_Asset_SCSS_ScssPhpCompiler {
    protected $importPaths = [];

    protected $style;

    public function __construct() {
        if (version_compare(PHP_VERSION, '7.2') < 0) {
            throw new \Exception('scssphp requires PHP 7.2 or above');
        }
    }

    public function setImportPaths($path) {
        $this->importPaths = carr::wrap($path);
    }

    /**
     * Compile scss.
     *
     * @param string      $code
     * @param null|array  $sourceMapOptions
     * @param null|string $path
     *
     * @return string
     */
    public function compile($code, $sourceMapOptions = null, $path = null) {
        $result = null;
        $scss = new Compiler();
        $scss->setImportPaths($this->importPaths);
        if ($this->style) {
            if ($this->style === OutputStyle::COMPRESSED || $this->style === OutputStyle::EXPANDED) {
                $scss->setOutputStyle($this->style);
            } else {
                throw new Exception('Error: the ' . $this->style . ' style is invalid.');
            }
        }

        if ($sourceMapOptions) {
            $scss->setSourceMap(Compiler::SOURCE_MAP_FILE);
            $scss->setSourceMapOptions($sourceMapOptions);

            try {
                $result = $scss->compileString($code, $path);
            } catch (SassException $e) {
                throw $e;
            }
        }

        return $result;
    }
}
