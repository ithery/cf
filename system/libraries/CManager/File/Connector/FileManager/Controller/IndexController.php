<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 1:40:19 AM
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_IndexController extends CManager_File_Connector_FileManager_AbstractController {
    public function execute() {
        $app = CApp::instance();
        $fm = $this->fm();
        CManager::registerCss('element/filemanager/fm.css');
        CManager::registerJs('element/filemanager/fm.js?v=1');
        $app->setViewName('cresenity/filemanager/index');
        $app->addTemplate()->setTemplate('CElement/Component/FileManager/Index')->setVar('fm', $fm);
        return $app;
    }
}
