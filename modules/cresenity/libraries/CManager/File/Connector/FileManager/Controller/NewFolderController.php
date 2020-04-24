<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 4:23:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_NewFolderController extends CManager_File_Connector_FileManager_AbstractController {

    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
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
            echo $e->getMessage();
            return;
        }
        echo parent::$successResponse;
    }

}
