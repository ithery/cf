<?php

defined('SYSPATH') or die('No direct access allowed.');

use League\Flysystem\UnableToRetrieveMetadata;

class CManager_File_Connector_FileManager_Controller_ItemController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Lists the files/folders in the current working directory.
     *
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();

        try {
            $data = [
                'items' => array_map(function ($item) {
                    return $item->fill()->attributes;
                }, array_merge($fm->path()->folders(), $fm->path()->files())),
                'display' => $fm->getDisplayMode(),
                'working_dir' => $fm->path()->path('working_dir'),
            ];
        } catch (UnableToRetrieveMetadata $e) {
            return c::abort(404);
        }

        return $this->successResponse($data);
    }
}
