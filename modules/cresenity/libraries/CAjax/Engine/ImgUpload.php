<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CAjax_Engine_ImgUpload extends CAjax_Engine {

    public function execute() {
        
        $data = $this->ajaxMethod->getData();
        $inputName = carr::get($data, 'inputName');
        $fileId = '';
        if (isset($_FILES[$inputName]) && isset($_FILES[$inputName]['name'])) {
            for ($i = 0; $i < count($_FILES[$inputName]['name']); $i++) {
                $extension = "." . pathinfo($_FILES[$inputName]['name'][$i], PATHINFO_EXTENSION);
                if (strtolower($extension) == 'php') {
                    die('fatal error');
                }
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $fullfilename = ctemp::makepath("imgupload", $fileId);
                if (!move_uploaded_file($_FILES[$inputName]['tmp_name'][$i], $fullfilename)) {
                    die('fail upload from ' . $_FILES[$inputName]['tmp_name'][$i] . ' to ' . $fullfilename);
                }
                $return[] = $fileId;
            }
        }

        if (isset($_POST[$inputName])) {

            $imageDataArray = $_POST[$inputName];
            $filenameArray = $_POST[$inputName . '_filename'];

            if (!is_array($imageDataArray)) {
                $imageDataArray = array($imageDataArray);
            }
            if (!is_array($filenameArray)) {
                $filenameArray = array($filenameArray);
            }
            foreach ($imageDataArray as $k => $imageData) {
                $filename = carr::get($filenameArray, $k);
                $extension = "." . pathinfo($filename, PATHINFO_EXTENSION);
                if (strtolower($extension) == 'php') {
                    die('fatal error');
                }

                $filteredData = substr($imageData, strpos($imageData, ",") + 1);
                $unencodedData = base64_decode($filteredData);
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                
                //$fullfilename = ctemp::makepath("imgupload", $fileId);
                //cfs::atomic_write($fullfilename, $unencodedData);
                $fullfilename = CTemporary::put('imgupload',$unencodedData,$fileId);
                $return[] = $fileId;
            }
        }
        $return = array(
            'fileId' => $fileId,
            'url' => ctemp::get_url('imgupload', $fileId),
        );
        return json_encode($return);
    }

}
