<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 9:49:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_RenameController extends CManager_File_Connector_FileManager_Controller_BaseController {

    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = new FM();
        $old_name = $fm->input('file');
        $new_name = $fm->input('new_name');
        $old_file = $fm->path()->pretty($old_name);
        $is_directory = $old_file->isDirectory();
        if (empty($new_name)) {
            if ($is_directory) {
                return parent::error('folder-name');
            } else {
                return parent::error('file-name');
            }
        }
        if ($fm->config('alphanumeric_directory') && preg_match('/[^\w-]/i', $new_name)) {
            return parent::error('folder-alnum');
            // return parent::error('file-alnum');
        } elseif ($fm->path()->setName($new_name)->exists()) {
            return parent::error('rename');
        }
        if (!$is_directory) {
            $extension = $old_file->extension();
            if ($extension) {
                $new_name = str_replace('.' . $extension, '', $new_name) . '.' . $extension;
            }
        }
        $new_file = $fm->path()->setName($new_name)->path('absolute');
        if ($is_directory) {
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderIsRenaming($old_file->path(), $new_file));
        } else {
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageIsRenaming($old_file->path(), $new_file));
        }
        if ($old_file->hasThumb()) {
            $fm->path()->setName($old_name)->thumb()
                    ->move($fm->path()->setName($new_name)->thumb());
        }
        $fm->path()->setName($old_name)
                ->move($fm->path()->setName($new_name));
        if ($is_directory) {
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_FolderWasRenamed($old_file->path(), $new_file));
        } else {
            $fm->dispatch(new CManager_File_Connector_FileManager_Event_ImageWasRenamed($old_file->path(), $new_file));
        }
        return parent::$successResponse;
    }

}
