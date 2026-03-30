<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Event_ImageWasResized {
    private $path;

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
