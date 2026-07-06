<?php

defined('SYSPATH') or die('No direct access allowed.');

use Intervention\Image\ImageManager;

class CManager_File_Connector_FileManager_Controller_ResizeImageController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();
        $imageName = $fm->input('img');
        $dataWidth = $fm->input('dataWidth');
        $dataHeight = $fm->input('dataHeight');
        $image_path = $fm->path()->setName($imageName)->path('absolute');

        try {
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageIsResizing($image_path));
            $imageManager = new ImageManager();
            $imageManager->make($image_path)->resize($dataWidth, $dataHeight)->save();
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageWasResized($image_path));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->successResponse();
    }
}
