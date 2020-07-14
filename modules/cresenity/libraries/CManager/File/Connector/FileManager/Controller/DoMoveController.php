<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 10:02:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
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
        foreach ($items as $item) {
            $old_file = $fm->path()->pretty($item);
            $is_directory = $old_file->isDirectory();
            if ($old_file->hasThumb()) {
                $new_file = $fm->path()->setName($item)->thumb()->dir($target);
                if ($is_directory) {
                    $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderIsMoving($old_file->path(), $new_file->path()));
                } else {
                    $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileIsMoving($old_file->path(), $new_file->path()));
                }
                $fm->path()->setName($item)->thumb()->move($new_file);
            }
            $new_file = $fm->path()->setName($item)->dir($target);
            $fm->path()->setName($item)->move($new_file);
            if ($is_directory) {
                $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderWasMoving($old_file->path(), $new_file->path()));
            } else {
                $fm->dispatch(new CManager_File_Connector_FileManager_Event_FileWasMoving($old_file->path(), $new_file->path()));
            }
        };
        echo parent::$successResponse;
    }

}
