<?php

defined('SYSPATH') or die('No direct access allowed.');

use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_IndexController extends CManager_File_Connector_FileManager_AbstractController {
    public function execute() {
        $app = c::app();
        $fm = $this->fm();
        CManager::registerCss('element/filemanager/fm.css');
        CManager::registerJs('element/filemanager/fm.js?v=1');
        $app->setViewName('cresenity/filemanager/index');
        $app->addView('cresenity.element.component.file-manager.index', [
            'fm' => $fm,
        ]);

        return $app;
    }
}
