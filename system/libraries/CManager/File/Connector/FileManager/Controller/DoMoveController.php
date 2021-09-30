<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 10:02:39 PM
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_DoMoveController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = $this->fm();
        $target = $fm->input('goToFolder');
        $items = $fm->input('items');
        if ((is_array($items))) {
            foreach ($items as $item) {
                $oldFile = $fm->path()->pretty($item);
                $isDirectory = $oldFile->isDirectory();
                if ($oldFile->hasThumb()) {
                    $newFile = $fm->path()->setName($item)->thumb()->dir($target);
                    $fm->path()->setName($item)->thumb()->move($newFile);
                    if ($isDirectory) {
                        $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderIsMoving($oldFile->path(), $newFile->path()));
                    } else {
                        $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileIsMoving($oldFile->path(), $newFile->path()));
                    }
                }
                $newFile = $fm->path()->setName($item)->dir($target);
                $fm->path()->setName($item)->move($newFile);
                if ($isDirectory) {
                    $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderWasMoved($oldFile->path(), $newFile->path()));
                } else {
                    $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileWasMoved($oldFile->path(), $newFile->path()));
                }
            }
        }

        return c::response(parent::$successResponse);
    }
}
