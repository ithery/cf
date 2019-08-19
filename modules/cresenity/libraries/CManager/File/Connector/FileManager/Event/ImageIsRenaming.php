<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 9:57:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Connector_FileManager_Event_ImageIsRenaming {

    private $old_path;
    private $new_path;

    public function __construct($old_path, $new_path) {
        $this->old_path = $old_path;
        $this->new_path = $new_path;
    }

    /**
     * @return string
     */
    public function oldPath() {
        return $this->old_path;
    }

    public function newPath() {
        return $this->new_path;
    }

}
