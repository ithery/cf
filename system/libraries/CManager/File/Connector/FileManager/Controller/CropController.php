<?php

defined('SYSPATH') or die('No direct access allowed.');

use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_CropController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = $this->fm();
        $app = CApp::instance();

        $workingdir = $fm->input('working_dir');

        $app->addView('cresenity.element.component.file-manager.cropper', [
            'fm' => $fm,
            'working_dir' => $workingdir,
            'img' => $fm->path()->pretty($fm->input('img'))
        ]);

        return $app;
    }
}
