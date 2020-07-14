<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CApp_Api_Method_Server_Temp_DeleteFile extends CApp_Api_Method_Server_Temp_Abstract {

    public function execute() {


        $errCode = 0;
        $errMessage = '';
        $domain = $this->method->domain();
        $request = $this->method->request();
        $file = carr::get($request, 'file');
        if(!cstr::startsWith($file, $this->basePath())) {
            $file = $this->basePath().ltrim($file, '/');
        }
        $data = [];
        $removed = false;
        if ($errCode == 0) {
            if (!file_exists($file)) {
                $errCode++;
                $errMessage = 'Failed to delete file, file not found';
            }
        }
        if ($errCode == 0) {
            $removed = @unlink($file);
        }
        if ($errCode == 0) {
            if ($removed === false) {
                $errCode++;
                $errMessage = 'Failed to delete file';
            }
        }

        if ($errCode > 0) {
            throw new CApp_Api_Exception_InternalException($errMessage);
        }

        return $data;
    }

}
