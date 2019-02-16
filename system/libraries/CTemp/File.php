<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 10:15:15 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTemp_File {

    /**
     *
     * @var CTemp_Directory 
     */
    protected $directory;

    /**
     *
     * @var CTemp_PathAbstract 
     */
    protected $pathEngine;

    /**
     *
     * @var string
     */
    protected $filename;

    public function __construct(CTemp_Directory $directory, $filename = null) {
        $this->directory = $directory;
        if ($filename == null) {
            $filename = date('YmdHis') . md5(uniqid()) . '.tmp';
        }

        $this->filename = $filename;
    }

    public function getContent() {
        return CFile::getContent($this->getPath());
    }

    public function setContent($content) {
        return CFile::setContent($this->getPath(), $content);
    }

    public function getPath() {
        return $this->directory->getPath() . '/' . $this->filename;
    }

    public function getUrl() {
        return $this->directory->getUrl() . '/' . $this->filename;
    }

}
