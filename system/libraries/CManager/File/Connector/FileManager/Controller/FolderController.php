<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_FolderController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = $this->fm();

        $app = c::app();
        $folderTypes = array_filter(['root'], function ($type) use ($fm) {
            return $fm->allowFolderType($type);
        });
        $rootFolders = array_map(function ($type) use ($folderTypes, $fm) {
            $path = $fm->path()->dir($fm->getRootFolder($type));

            return (object) [
                'name' => $type,
                'url' => $path->path('working_dir'),
                'children' => $path->folders(),
                'has_next' => !($type == end($folderTypes)),
            ];
        }, $folderTypes);
        $app->addView('cresenity.element.component.file-manager.tree', [
            'fm' => $fm,
            'rootFolders' => $rootFolders,
        ]);

        return $app;
    }
}
