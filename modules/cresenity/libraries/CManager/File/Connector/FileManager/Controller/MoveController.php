<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 10:01:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_MoveController extends CManager_File_Connector_FileManager_Controller_BaseController {

    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = new FM();
        $app = CApp::instance();
        $items = $fm->input('items');
        $folder_types = array_filter(['user', 'share'], function ($type) use($fm) {
            return $fm->allowFolderType($type);
        });

        $rootFolders = array_map(function ($type) use ($folder_types, $fm) {
            $path = $fm->path()->dir($fm->getRootFolder($type));
            return (object) [
                        'name' => clang::__('filemanager.title-' . $type),
                        'url' => $path->path('working_dir'),
                        'children' => $path->folders(),
                        'has_next' => !($type == end($folder_types)),
            ];
        }, $folder_types);

        $app->addTemplate()->setTemplate('CElement/Component/FileManager/Move')->setVar('fm', $fm)
                ->setVar('rootFolders', $rootFolders)
                ->setVar('items', $items);
        echo $app->render();
    }

}
