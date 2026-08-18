<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_DeleteController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Deletes one or more files/folders, collecting per-item errors instead of
     * aborting the whole batch on the first failure.
     *
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();
        $item_names = $fm->input('items');
        $errors = [];
        foreach ($item_names as $nameToDelete) {
            $file_to_delete = $fm->path()->pretty($nameToDelete);
            $filePath = $file_to_delete->path();
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileIsDeleting($filePath));

            try {
                if (is_null($nameToDelete)) {
                    $fm->error('folder-name');
                }
                if (!$fm->path()->setName($nameToDelete)->exists()) {
                    $fm->error('folder-not-found', ['folder' => $filePath]);
                }

                if ($fm->path()->setName($nameToDelete)->isDirectory()) {
                    if (!$fm->path()->setName($nameToDelete)->directoryIsEmpty()) {
                        $fm->error('delete-folder');
                    }
                } elseif ($file_to_delete->isImage()) {
                    $fm->path()->setName($nameToDelete)->thumb()->delete();
                }

                $fm->path()->setName($nameToDelete)->delete();
                $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileWasDeleted($filePath));
            } catch (\League\Flysystem\UnableToDeleteFile $ex) {
                // do nothing on UnableToDeleteFile
            } catch (\League\Flysystem\UnableToDeleteDirectory $ex) {
                // do nothing on UnableToDeleteDirectory
            } catch (\Exception $ex) {
                $errors[] = $ex->getMessage();
            }
        }
        if (count($errors) > 0) {
            return $this->errorResponse(implode("\n", $errors));
        }

        return $this->successResponse();
    }
}
