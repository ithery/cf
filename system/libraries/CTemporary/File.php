<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 10:15:15 PM
 */
class CTemporary_File {
    /**
     * @var CTemp_Directory
     */
    protected $directory;

    /**
     * @var CTemp_PathAbstract
     */
    protected $pathEngine;

    /**
     * @var CFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $filename;

    public function __construct(CTemporary_Directory $directory, $filename = null) {
        $this->directory = $directory;
        if ($filename == null) {
            $filename = date('YmdHis') . md5(uniqid()) . '.tmp';
        }

        $this->filename = $filename;
        $this->file = new CFile();
    }

    public function get($lock = false) {
        return $this->file->get($this->getPath(), $lock);
    }

    public function put($content, $lock = false) {
        return $this->file->put($this->getPath(), $content, $lock);
    }

    public function exists() {
        return $this->file->exists($this->getPath());
    }

    public function delete() {
        return $this->file->delete($this->getPath());
    }

    public function getPath() {
        return $this->directory->getPath() . '/' . $this->filename;
    }

    public function getUrl() {
        return $this->directory->getUrl() . '/' . $this->filename;
    }
}
