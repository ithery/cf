<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 12, 2019, 12:58:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;
use Intervention\Image\ImageManager;

class CManager_File_Connector_FileManager_Controller_ResizeImageController extends CManager_File_Connector_FileManager_Controller_BaseController {

    public function execute() {
        $fm = new FM();
        $imageName = $fm->input('img');
        $dataWidth = $fm->input('dataWidth');
        $dataHeight = $fm->input('dataHeight');
        $image_path = $fm->path()->setName($imageName)->path('absolute');

        $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageIsResizing($image_path));
        $imageManager = new ImageManager();
        $imageManager->make($image_path)->resize($dataWidth, $dataHeight)->save();
        $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageWasResized($image_path));
        echo parent::$successResponse;
    }

}
