<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jul 30, 2020 
 * @license Ittron Global Teknologi
 */
class CManager_Asset_Compiler {

    use CTrait_HasOptions;

    protected $files;
    protected $outFile;
    protected $type;

    /**
     * The last modified time of the newest Asset in the Assets array
     * @var int
     */
    protected $lastModTimeNewestAsset = 0;

    /**
     * The last modified time of the compiled Asset 
     * @var int
     */
    protected $lastModTimeCompiledAsset = 0;
    protected $separator = "\n";

    public function __construct(array $files, $options = []) {

        $files = carr::map($files, function($script) {
                    return preg_replace('/\?.*/', '', $script);
                });

        $this->files = $files;
        $this->options = $options;
        $this->type = carr::get($options, 'type');

        $this->outFile = carr::get($options, 'outFile');


        if ($this->type == null) {
            $this->determineType();
        }
        if ($this->outFile == null) {
            $this->determineOutFile();
        }
        $this->determineLastModified();
    }

    protected function determineType() {
        $firstFile = carr::first($this->files);
        $extension = pathinfo($firstFile, PATHINFO_EXTENSION);
        $this->type = strtolower($extension);
    }

    protected function determineOutFile() {
        $this->outFile = DOCROOT . 'compiled/asset/' . $this->type . '/' . md5(implode(":", $this->files)) . '.' . $this->type;
    }

    protected function determineLastModified() {
        //Set the instance variable to store the last modified time of the newest file
        $this->lastModTimeNewestAsset = 0;
        foreach ($this->files as $file) {
            if (!file_exists($file)) {
                throw new Exception('Error to compile asseet, ' . $file . ' not exist');
            }
            $mTime = filemtime($file);
            $this->lastModTimeNewestAsset = $mTime > $this->lastModTimeNewestAsset ? $mTime : $this->lastModTimeNewestAsset;
        }

        $this->lastModTimeCompiledAsset = 0;
        if (file_exists($this->outFile)) {
            $this->lastModTimeCompiledAsset = filemtime($this->outFile);
        }
    }

    protected function outputPath() {
        return 'compiled';
    }

    public function needToRecompile() {
        return $this->lastModTimeCompiledAsset < $this->lastModTimeNewestAsset;
    }

    public function compile() {
        if ($this->needToRecompile()) {
            $dirname = dirname($this->outFile);
            if (!is_dir($dirname)) {
                cfs::mkdir($dirname);
            }

            file_put_contents($this->outFile, '');
            foreach ($this->files as $file) {
                file_put_contents($this->outFile, $this->separator . file_get_contents($file), FILE_APPEND);
            }
        }
        return $this->outFile.'?v='.filemtime($this->outFile);
    }

}
