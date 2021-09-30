<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 *
 * @since Aug 11, 2019, 9:39:32 PM
 *
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
        foreach ($item_names as $nameToDelete) {
            $file_to_delete = $fm->path()->pretty($nameToDelete);
            $filePath = $file_to_delete->path();
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageIsDeleting($filePath));
            try {
                if (is_null($nameToDelete)) {
                    array_push($errors, parent::error('folder-name'));
                    continue;
                }
                if (!$fm->path()->setName($nameToDelete)->exists()) {
                    array_push($errors, parent::error('folder-not-found', ['folder' => $filePath]));
                    continue;
                }

                if ($fm->path()->setName($nameToDelete)->isDirectory()) {
                    if (!$fm->path()->setName($nameToDelete)->directoryIsEmpty()) {
                        array_push($errors, parent::error('delete-folder'));
                        continue;
                    }
                } else {
                    if ($file_to_delete->isImage()) {
                        $fm->path()->setName($nameToDelete)->thumb()->delete();
                    }
                }

                $fm->path()->setName($nameToDelete)->delete();
            } catch (\League\Flysystem\FileNotFoundException $ex) {
                // do nothing on FileNotFoundException
            } catch (Exception $ex) {
                throw $ex;
            }

            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageWasDeleted($filePath));
        }
        if (count($errors) > 0) {
            echo json_encode($errors);
            return;
        }
        echo parent::$successResponse;
    }
}
