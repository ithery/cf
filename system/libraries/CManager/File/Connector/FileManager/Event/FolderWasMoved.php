<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Event_FolderWasMoved {
    /**
     * @var string
     */
    private $oldPath;

    /**
     * @var string
     */
    private $newPath;

    /**
     * @param string $oldPath
     * @param string $newPath
     */
    public function __construct($oldPath, $newPath) {
        $this->oldPath = $oldPath;
        $this->newPath = $newPath;
    }

    /**
     * @return string
     */
    public function oldPath() {
        return $this->oldPath;
    }

    /**
     * @return string
     */
    public function newPath() {
        return $this->newPath;
    }
}
