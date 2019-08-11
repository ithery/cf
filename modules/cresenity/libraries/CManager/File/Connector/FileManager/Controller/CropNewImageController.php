<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 12, 2019, 12:20:10 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;
use Intervention\Image\ImageManager;

class CManager_File_Connector_FileManager_Controller_CropNewImageController extends CManager_File_Connector_FileManager_Controller_CropImageController {

    public function execute() {
        $this->crop(false);
    }

}
