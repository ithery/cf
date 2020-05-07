<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CApp_Api_Method_Server_TempGetFileList extends CApp_Api_Method_Server {

    public function execute() {


        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $request = $this->request();
        $directory = carr::get($request, 'directory');
        $allFiles = cfs::list_files(DOCROOT .'temp/'. ltrim($directory,'/'));
        $files = array();
        foreach ($allFiles as $filename) {


            $file = array(
                'filename' => $filename,
                'created' => date('Y-m-d H:i:s', filemtime($filename)),
            );
            $files[] = $file;
        }
        $data = array();
        $data['list'] = $files;
        $data['count'] = count($files);

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}