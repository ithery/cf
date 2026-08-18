<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_NewFolderController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Creates a new folder in the current working directory.
     *
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();
        $folder_name = $fm->input('name');

        try {
            if (empty($folder_name)) {
                $fm->error('folder-name');
            } elseif ($fm->path()->setName($folder_name)->exists()) {
                $fm->error('folder-exist');
            } elseif ($fm->config('alphanumeric_directory') && preg_match('/[^\w-]/i', $folder_name)) {
                $fm->error('folder-alnum');
            } else {
                $fm->path()->setName($folder_name)->createFolder();
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->successResponse();
    }
}
