<?php

defined('SYSPATH') or die('No direct access allowed.');

use Intervention\Image\ImageManager;
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_CropNewImageController extends CManager_File_Connector_FileManager_Controller_CropImageController {
    public function execute() {
        $this->crop(false);
    }
}
