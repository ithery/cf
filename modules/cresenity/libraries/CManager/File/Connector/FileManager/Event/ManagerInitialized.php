<?php

class CManager_File_Connector_FileManager_Event_ManagerInitialized {
    protected $fm;

    public function __construct(CManager_File_Connector_FileManager_FM $fm) {
        $this->fm = $fm;
    }

    /**
     * @return CManager_File_Connector_FileManager_FM
     */
    public function fm() {
        return $this->fm;
    }
}
