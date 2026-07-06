<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_FolderController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Lists folders for the sidebar tree, built up lazily: without a `path`
     * input, returns the top-level allowed root folders (e.g. 'root'/'user');
     * with one, returns that folder's own direct subfolders. Each entry's
     * `has_children` tells the client whether to render an expand caret, so
     * FileManager.js only ever fetches one level deeper than what's already
     * visible (see loadFolders()/expandTreeNode() there).
     *
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();
        $path = $fm->input('path');

        if ($path !== null && $path !== '') {
            $folders = $fm->path()->dir($path)->folders();

            return $this->successResponse(['folders' => $this->foldersToArray($folders)]);
        }

        $folderTypes = array_filter(['root'], function ($type) use ($fm) {
            return $fm->allowFolderType($type);
        });

        return $this->successResponse(['folders' => $this->rootFoldersToArray($folderTypes, $fm)]);
    }

    /**
     * @param CManager_File_Connector_FileManager_FM_Item[] $folders
     *
     * @return array
     */
    protected function foldersToArray($folders) {
        $fm = $this->fm();

        return array_values(array_map(function ($folder) use ($fm) {
            return [
                'name' => $folder->name(),
                'path' => $folder->url(),
                'has_children' => count($fm->path()->dir($folder->url())->folders()) > 0,
            ];
        }, $folders));
    }

    /**
     * @param string[]                              $folderTypes
     * @param CManager_File_Connector_FileManager_FM $fm
     *
     * @return array
     */
    protected function rootFoldersToArray($folderTypes, $fm) {
        return array_values(array_map(function ($type) use ($fm) {
            $path = $fm->path()->dir($fm->getRootFolder($type));

            return [
                'name' => $type,
                'path' => $path->path('working_dir'),
                'has_children' => count($path->folders()) > 0,
            ];
        }, $folderTypes));
    }
}
