<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use CManager_File_Connector_FileManager_FM as FM;
use \Illuminate\Support\Facades\Facade as Facade;

class CManager_File_Connector_FileManager_AbstractController {

    protected static $successResponse = 'OK';

    /**
     *
     * @var CManager_File_Connector_FileManager 
     */
    protected $fileManager;
    protected $fm;

    public function __construct(CManager_File_Connector_FileManager $fileManager) {
        $this->fileManager = $fileManager;
        $app = CApp::instance();
        $app->setLoginRequired(false);
        CManager::theme()->setThemeCallback(function($theme) {
            return $this->fm()->config('theme', 'cresenity-filemanager');
        });

        //do this with facade
        //Facade::setFacadeApplication(CContainer::getInstance());
    }

    /**
     * 
     * @return CManager_File_Connector_FileManager_FM
     */
    protected function fm() {
        if ($this->fm == null) {
            $this->fm = new FM($this->fileManager->getConfig());
        }
        return $this->fm;
    }

    public function error($error_type, $variables = []) {

        return $this->fm()->error($error_type, $variables);
    }

}
