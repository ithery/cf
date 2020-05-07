<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CApp_Api_Method_Server_Temp extends CApp_Api_Method_Server {

    public function execute() {


        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $request = $this->request();
        $data = [];

        $command = carr::get($request, 'command');
        $classCommand = null;
        try {
            switch ($command) {
                case 'deleteFile':
                    $classCommand = CApp_Api_Method_Server_Temp_DeleteFile::class;
                    break;
                case 'listFile':
                    $classCommand = CApp_Api_Method_Server_Temp_ListFile::class;
                    break;
                case 'content':
                    $classCommand = CApp_Api_Method_Server_Temp_Content::class;
                    break;
            }
            
            if(strlen($classCommand)==0 || !class_exists($classCommand)) {
                $this->errCode++;
                $this->errMessage = 'Command '.$command.' not found';
            } 
            if($this->errCode==0) {
                $commandObject = new $classCommand($this);
                $data= $commandObject->execute($request);
            }
        } catch (Exception $ex) {
            $this->errCode++;
            $this->errMessage = $ex->getMessage();
        }

        $this->data = $data;

        return $this;
    }

}
