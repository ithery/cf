<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_IndexController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * @return \CApp
     */
    public function execute() {
        $app = c::app();
        $fm = $this->fm();
        $app->setViewName('cresenity/filemanager/index');
        $app->addView('cresenity.element.component.file-manager.index', [
            'fm' => $fm,
        ]);

        return $app;
    }
}
