<?php

use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_DownloadController extends CManager_File_Connector_FileManager_AbstractController {
    public function execute() {
        $fm = $this->fm();
        $file = $fm->input('file');
        $path = $fm->path()->setName($file);

        cdownload::force($file, $path->get());
    }
}
