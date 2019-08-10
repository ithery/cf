<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 1:41:50 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Connector_FileManager_Controller_BaseController {

    protected static $successResponse = 'OK';
    
    public function __construct() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        CManager::theme()->setThemeCallback(function($theme){
            return 'cresenity-filemanager';
        });
        
    }

}
