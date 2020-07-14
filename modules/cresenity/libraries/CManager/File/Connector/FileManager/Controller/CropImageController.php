<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 12, 2019, 12:19:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;
use Intervention\Image\ImageManager;

class CManager_File_Connector_FileManager_Controller_CropImageController extends CManager_File_Connector_FileManager_AbstractController {

   
    public function execute() {
        $this->crop(true);
    }

    /**
     * Crop the image (called via ajax).
     */
    public function crop($overWrite = true) {
        $fm = $this->fm();
        $image_name = $fm->input('img');
        $image_path = $fm->path()->setName($image_name)->path('absolute');
        $crop_path = $image_path;
        if (!$overWrite) {
            $fileParts = explode('.', $image_name);
            $fileParts[count($fileParts) - 2] = $fileParts[count($fileParts) - 2] . '_cropped_' . time();
            $crop_path = $fm->path()->setName(implode('.', $fileParts))->path('absolute');
        }

        $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageIsCropping($image_path));
        $cropInfo = CF::collect(CHTTP::request()->input())->only('dataWidth', 'dataHeight', 'dataX', 'dataY');
        $width = carr::get($cropInfo,'dataWidth');
        $height = carr::get($cropInfo,'dataHeight');
        $x = carr::get($cropInfo,'dataX');
        $y = carr::get($cropInfo,'dataY');
        
        $imageManager = new ImageManager();
        $imageManager->make($image_path)
                ->crop($width,$height,$x,$y)
                ->save($crop_path);
        // make new thumbnail
        $fm->path()->makeThumbnail($image_name);
        $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageWasCropped($image_path));
        
        echo parent::$successResponse;
    }

}
