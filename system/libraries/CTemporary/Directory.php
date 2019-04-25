<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 10:16:40 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTemporary_Directory {

    protected $path;

    public function __construct($path) {
        $this->path = 'temp/' . trim($path, '/');
        CFile::makeDirectory($this->getPath(), 0755, true, true);
    }

    public function getPath() {
        return rtrim(DOCROOT, '/') . '/' . rtrim($this->path, '/');
    }

    public function getUrl() {
        return rtrim(curl::base(), '/') . '/' . rtrim($this->path, '/');
    }

    public function createFile($filename) {
        return new CTemporary_File($this, $filename);
    }

}
