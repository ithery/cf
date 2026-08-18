<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_DoMoveController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Moves one or more files/folders into the chosen destination folder
     * (the picker itself is rendered by MoveController).
     *
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();
        $target = $fm->input('goToFolder');
        $items = $fm->input('items');

        try {
            if (is_array($items)) {
                foreach ($items as $item) {
                    $oldFile = $fm->path()->pretty($item);

                    $isDirectory = $oldFile->isDirectory();
                    $newFile = $fm->path()->setName($item)->dir($target);
                    if ($isDirectory) {
                        $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderIsMoving($oldFile->path(), $newFile->path()));
                    } else {
                        $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileIsMoving($oldFile->path(), $newFile->path()));
                    }
                    if ($oldFile->hasThumb()) {
                        $newThumbFile = $fm->path()->setName($item)->thumb()->dir($target);
                        $fm->path()->setName($item)->thumb()->move($newThumbFile);
                    }

                    $fm->path()->setName($item)->move($newFile);
                    if ($isDirectory) {
                        $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderWasMoved($oldFile->path(), $newFile->path()));
                    } else {
                        $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileWasMoved($oldFile->path(), $newFile->path()));
                    }
                }
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        return $this->successResponse();
    }
}
