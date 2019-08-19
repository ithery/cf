<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 9:57:53 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Connector_FileManager_Event_ImageIsResizing {

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
