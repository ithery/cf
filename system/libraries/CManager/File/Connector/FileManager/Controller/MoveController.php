<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_MoveController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Lists the allowed root folder(s) and their direct children, used by the
     * client to render the folder-picker dialog (see showMovePicker() in
     * FileManager.js) before the actual move (see DoMoveController).
     *
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();
        $folder_types = array_filter(['root'], function ($type) use ($fm) {
            return $fm->allowFolderType($type);
        });

        $rootFolders = array_values(array_map(function ($type) use ($fm) {
            $path = $fm->path()->dir($fm->getRootFolder($type));

            return [
                'name' => $type,
                'path' => $path->path('working_dir'),
                'children' => array_values(array_map(function ($child) {
                    return [
                        'name' => $child->name(),
                        'path' => $child->url(),
                    ];
                }, $path->folders())),
            ];
        }, $folder_types));

        return $this->successResponse(['folders' => $rootFolders]);
    }
}
