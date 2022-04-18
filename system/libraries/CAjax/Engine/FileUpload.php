<?php

class CAjax_Engine_FileUpload extends CAjax_Engine {
    public function execute() {
        $data = $this->ajaxMethod->getData();
        $inputName = carr::get($data, 'inputName');
        $fileId = '';
        $fileName = '';
        if (isset($_FILES[$inputName], $_FILES[$inputName]['name'])) {
            for ($i = 0; $i < count($_FILES[$inputName]['name']); $i++) {
                $fileName = $_FILES[$inputName]['name'][$i];
                $ext = pathinfo($_FILES[$inputName]['name'][$i], PATHINFO_EXTENSION);
                if (strlen($ext) > 3) {
                    $ext = substr($ext, 0, 3);
                }
                if (in_array($ext, ['php', 'sh', 'htm', 'pht'])) {
                    die('Not Allowed X_X');
                }

                $extension = '.' . $ext;

                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $disk = CTemporary::disk();
                $fullfilename = CTemporary::getPath('fileupload', $fileId);

                if (!$disk->put($fullfilename, file_get_contents($_FILES[$inputName]['tmp_name'][$i]))) {
                    die('fail upload from ' . $_FILES[$inputName]['tmp_name'][$i] . ' to ' . $fullfilename);
                }
                $return[] = $fileId;
            }
        }

        if (isset($_POST[$inputName])) {
            $fileDataArray = $_POST[$inputName];
            $filenameArray = $_POST[$inputName . '_filename'];

            if (!is_array($fileDataArray)) {
                $fileDataArray = [$fileDataArray];
            }
            if (!is_array($filenameArray)) {
                $filenameArray = [$filenameArray];
            }
            foreach ($fileDataArray as $k => $fileData) {
                $fileName = carr::get($filenameArray, $k);

                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                if (strlen($ext) > 3) {
                    $ext = substr($ext, 0, 3);
                }
                if (in_array($ext, ['php', 'sh', 'htm', 'pht'])) {
                    die('Not Allowed X_X');
                }
                $extension = '.' . $ext;

                $filteredData = substr($fileData, strpos($fileData, ',') + 1);
                $unencodedData = base64_decode($filteredData);
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $fullfilename = CTemporary::getPath('fileupload', $fileId);
                $disk = CTemporary::disk();
                $disk->put($fullfilename, $unencodedData);

                $return[] = $fileId;
            }
        }
        $return = [
            'fileId' => $fileId,
            'fileName' => $fileName,
            'url' => CTemporary::getUrl('fileupload', $fileId),
        ];

        return json_encode($return);
    }
}
