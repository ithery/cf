<?php

class CAjax_Engine_ImgUpload extends CAjax_Engine {
    const FOLDER = 'imgupload';

    public function execute() {
        $data = $this->ajaxMethod->getData();
        $inputName = carr::get($data, 'inputName');
        $allowedExtension = carr::get($data, 'allowedExtension', []);
        $validationCallback = carr::get($data, 'validationCallback');
        $fileId = '';
        if (isset($_FILES[$inputName], $_FILES[$inputName]['name'])) {
            for ($i = 0; $i < count($_FILES[$inputName]['name']); $i++) {
                $ext = pathinfo($_FILES[$inputName]['name'][$i], PATHINFO_EXTENSION);

                if (strlen($ext) > 3) {
                    $ext = substr($ext, 0, 3);
                }
                if (in_array($ext, ['php', 'sh', 'htm', 'pht'])) {
                    die('Not Allowed X_X');
                }
                if ($allowedExtension) {
                    if (!in_array($ext, $allowedExtension)) {
                        die('Not Allowed X_X');
                    }
                }
                if ($validationCallback && $validationCallback instanceof Opis\Closure\SerializableClosure) {
                    $validationCallback->__invoke($_FILES[$inputName]['name'][$i], $_FILES[$inputName]['tmp_name'][$i]);
                }

                $extension = '.' . $ext;
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $disk = CTemporary::disk();
                $fullfilename = CTemporary::getPath('imgupload', $fileId);

                if (!$disk->put($fullfilename, file_get_contents($_FILES[$inputName]['tmp_name'][$i]))) {
                    die('fail upload from ' . $_FILES[$inputName]['tmp_name'][$i] . ' to ' . $fullfilename);
                }
                $return[] = $fileId;
            }
        }

        if (isset($_POST[$inputName])) {
            $imageDataArray = $_POST[$inputName];
            $filenameArray = $_POST[$inputName . '_filename'];

            if (!is_array($imageDataArray)) {
                $imageDataArray = [$imageDataArray];
            }
            if (!is_array($filenameArray)) {
                $filenameArray = [$filenameArray];
            }
            foreach ($imageDataArray as $k => $imageData) {
                $filename = carr::get($filenameArray, $k);
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (strlen($ext) > 3) {
                    $ext = substr($ext, 0, 3);
                }
                if (in_array($ext, ['php', 'sh', 'htm', 'pht'])) {
                    die('Not Allowed X_X');
                }
                if ($allowedExtension) {
                    if (!in_array($ext, $allowedExtension)) {
                        die('Not Allowed X_X');
                    }
                }
                if ($validationCallback && $validationCallback instanceof Opis\Closure\SerializableClosure) {
                    $validationCallback->__invoke($filename, $imageData);
                }

                $extension = '.' . $ext;
                $filteredData = substr($imageData, strpos($imageData, ',') + 1);
                $unencodedData = base64_decode($filteredData);
                $fileId = date('Ymd') . cutils::randmd5() . $extension;

                $fullfilename = CTemporary::put(static::FOLDER, $unencodedData, $fileId);

                $return[] = $fileId;
            }
        }
        $return = [
            'fileId' => $fileId,
            'url' => CTemporary::getUrl(static::FOLDER, $fileId),
        ];

        return json_encode($return);
    }
}
