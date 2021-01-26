<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 10:16:40 PM
 */
class CTemporary_Directory {
    protected $path;

    public function __construct($path) {
        $this->path = 'temp' . DS . trim($path, DS);
        CFile::makeDirectory($this->getPath(), 0777, true, true);
    }

    protected function getBasePath() {
        return rtrim(DOCROOT, DS) . DS . rtrim($this->path, DS) . DS;
    }

    public function getPath($pathOrFilename = '') {
        if (empty($pathOrFilename)) {
            return $this->getBasePath();
        }

        $path = rtrim($this->getBasePath(), DS) . DS . trim($pathOrFilename, DS);
        $directoryPath = $this->removeFilenameFromPath($path);
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        return $path;
    }

    public function getUrl() {
        return rtrim(curl::base(), '/') . '/' . rtrim($this->path, '/');
    }

    public function createFile($filename) {
        return new CTemporary_File($this, $filename);
    }

    public function delete() {
        CFile::deleteDirectory($this->getPath());
    }

    protected function removeFilenameFromPath($path) {
        if (!$this->isFilePath($path)) {
            return $path;
        }
        return substr($path, 0, strrpos($path, DS));
    }

    protected function isFilePath($path) {
        return strpos($path, '.') !== false;
    }
}
