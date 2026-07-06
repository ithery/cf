<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_CropNewImageController extends CManager_File_Connector_FileManager_Controller_CropImageController {
    /**
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        return $this->crop(false);
    }
}
