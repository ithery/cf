<?php

defined('SYSPATH') or die('No direct access allowed.');

use Intervention\Image\ImageManager;
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_ResizeImageController extends CManager_File_Connector_FileManager_AbstractController {
    public function execute() {
        $fm = $this->fm();
        $imageName = $fm->input('img');
        $dataWidth = $fm->input('dataWidth');
        $dataHeight = $fm->input('dataHeight');
        $image_path = $fm->path()->setName($imageName)->path('absolute');

        $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageIsResizing($image_path));
        $imageManager = new ImageManager();
        $imageManager->make($image_path)->resize($dataWidth, $dataHeight)->save();
        $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageWasResized($image_path));

        return c::response(parent::$successResponse);
    }
}
