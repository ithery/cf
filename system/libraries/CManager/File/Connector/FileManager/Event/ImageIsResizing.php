<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Event_ImageIsResizing {
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function path() {
        return $this->path;
    }
}
