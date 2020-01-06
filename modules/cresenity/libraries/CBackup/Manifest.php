<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Manifest implements Countable {

    /** @var string */
    protected $manifestPath;

    public static function create($manifestPath) {
        return new static($manifestPath);
    }

    public function __construct($manifestPath) {
        $this->manifestPath = $manifestPath;
        touch($manifestPath);
    }

    public function path() {
        return $this->manifestPath;
    }

    /**
     * @param array|string $filePaths
     *
     * @return $this
     */
    public function addFiles($filePaths) {
        if (is_string($filePaths)) {
            $filePaths = [$filePaths];
        }
        foreach ($filePaths as $filePath) {
            if (!empty($filePath)) {
                file_put_contents($this->manifestPath, $filePath . PHP_EOL, FILE_APPEND);
            }
        }
        return $this;
    }

    /**
     * @return \Generator|string[]
     */
    public function files() {
        $file = new SplFileObject($this->path());
        while (!$file->eof()) {
            $filePath = $file->fgets();
            if (!empty($filePath)) {
                yield trim($filePath);
            }
        }
    }

    public function count() {
        $file = new SplFileObject($this->manifestPath, 'r');
        $file->seek(PHP_INT_MAX);
        return $file->key();
    }

}
