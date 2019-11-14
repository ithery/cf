<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CAjax_Engine_FileUpload extends CAjax_Engine {

    public function execute() {
        $data = $this->ajaxMethod->getData();
        $inputName = carr::get($data, 'inputName');
        $fileId = '';

        if (isset($_FILES[$inputName]) && isset($_FILES[$inputName]['name'])) {
            for ($i = 0; $i < count($_FILES[$inputName]['name']); $i++) {
                $fileName = $_FILES[$inputName]['name'][$i];
                $extension = "." . pathinfo($fileName, PATHINFO_EXTENSION);
                if (strtolower($extension) == 'php') {
                    die('fatal error');
                }
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $fullfilename = ctemp::makepath("fileupload", $fileId);
                if (!move_uploaded_file($_FILES[$inputName]['tmp_name'][$i], $fullfilename)) {
                    die('fail upload from ' . $_FILES[$inputName]['tmp_name'][$i] . ' to ' . $fullfilename);
                }
                $return[] = $fileId;
            }
        }

        if (isset($_POST[$inputName])) {
            $fileDataArray = $_POST[$inputName];
            $filenameArray = $_POST[$inputName . '_filename'];

            if (!is_array($fileDataArray)) {
                $fileDataArray = array($fileDataArray);
            }
            if (!is_array($filenameArray)) {
                $filenameArray = array($filenameArray);
            }
            foreach ($fileDataArray as $k => $fileData) {
                $fileName = carr::get($filenameArray, $k);
                $extension = "." . pathinfo($fileName, PATHINFO_EXTENSION);
                if (strtolower($extension) == 'php') {
                    die('fatal error');
                }

                $filteredData = substr($fileData, strpos($fileData, ",") + 1);
                $unencodedData = base64_decode($filteredData);
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $fullfilename = ctemp::makepath("fileupload", $fileId);
                cfs::atomic_write($fullfilename, $unencodedData);
                $return[] = $fileId;
            }
        }
        $return = array(
            'fileId' => $fileId,
            'fileName' => $fileName,
            'url' => ctemp::get_url('fileupload', $fileId),
        );
        return json_encode($return);
    }

}
