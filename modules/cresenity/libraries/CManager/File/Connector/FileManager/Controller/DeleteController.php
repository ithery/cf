<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 9:39:32 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_DeleteController extends CManager_File_Connector_FileManager_AbstractController {

    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = $this->fm();
        $item_names = $fm->input('items');
        $errors = [];
        foreach ($item_names as $name_to_delete) {
            $file_to_delete = $fm->path()->pretty($name_to_delete);
            $file_path = $file_to_delete->path();
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageIsDeleting($file_path));
            if (is_null($name_to_delete)) {
                array_push($errors, parent::error('folder-name'));
                continue;
            }
            if (!$fm->path()->setName($name_to_delete)->exists()) {
                array_push($errors, parent::error('folder-not-found', ['folder' => $file_path]));
                continue;
            }
            if ($fm->path()->setName($name_to_delete)->isDirectory()) {
                if (!$fm->path()->setName($name_to_delete)->directoryIsEmpty()) {
                    array_push($errors, parent::error('delete-folder'));
                    continue;
                }
            } else {
                if ($file_to_delete->isImage()) {
                    $fm->path()->setName($name_to_delete)->thumb()->delete();
                }
            }
            $fm->path()->setName($name_to_delete)->delete();

            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageWasDeleted($file_path));
        }
        if (count($errors) > 0) {
            echo json_encode($errors);
            return;
        }
        echo parent::$successResponse;
    }

}
