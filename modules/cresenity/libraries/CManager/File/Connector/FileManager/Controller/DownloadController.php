<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_DownloadController extends CManager_File_Connector_FileManager_AbstractController {

    public function execute() {
        $fm = $this->fm();
        $file = $fm->input('file');
        $path = $fm->path()->setName($file);
        
        cdownload::force($file,$path->get());
        
    }

}
