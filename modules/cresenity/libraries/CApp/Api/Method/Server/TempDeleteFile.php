<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CApp_Api_Method_Server_TempDeleteFile extends CApp_Api_Method_Server {

    public function execute() {


        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $request = $this->request();
        $file = carr::get($request, 'file');
        $file = DOCROOT . 'temp/' . ltrim($file, '/');
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

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
