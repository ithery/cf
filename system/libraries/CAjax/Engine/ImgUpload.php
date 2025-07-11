<?php

class CAjax_Engine_ImgUpload extends CAjax_Engine {
    use CAjax_Trait_UploadTrait;

    const FOLDER = 'imgupload';

    const FOLDER_INFO = 'imguploadinfo';

    public function execute() {
        $data = $this->ajaxMethod->getData();
        $inputName = carr::get($data, 'inputName');
        $allowedExtension = carr::get($data, 'allowedExtension', []);
        $validationCallback = carr::get($data, 'validationCallback');
        $withInfo = carr::get($data, 'withInfo', false);
        $diskName = carr::get($data, 'disk', CF::config('storage.temp'));
        $fileId = '';
        $fileName = '';

        if (isset($_FILES[$inputName], $_FILES[$inputName]['name'])) {
            for ($i = 0; $i < count($_FILES[$inputName]['name']); $i++) {
                $fileName = $_FILES[$inputName]['name'][$i];
                //$fileSize = $_FILES[$inputName]['size'][$i];
                $ext = pathinfo($_FILES[$inputName]['name'][$i], PATHINFO_EXTENSION);
                $this->checkExtension($ext, $allowedExtension);
                if ($validationCallback && $validationCallback instanceof Opis\Closure\SerializableClosure) {
                    $validationCallback->__invoke($_FILES[$inputName]['name'][$i], $_FILES[$inputName]['tmp_name'][$i]);
                }

                $extension = '.' . $ext;
                $fileId = $this->generateFileId($extension);

                $disk = CTemporary::publicDisk();
                $fullfilename = CTemporary::getPath(static::FOLDER, $fileId);
                if (!isset($_FILES[$inputName]['tmp_name'][$i]) || empty($_FILES[$inputName]['tmp_name'][$i])) {
                    CLogger::channel()->error('Error on ImgUpload', (array) $_FILES);
                }
                if (!$disk->put($fullfilename, file_get_contents($_FILES[$inputName]['tmp_name'][$i]))) {
                    die('fail upload from ' . $_FILES[$inputName]['tmp_name'][$i] . ' to ' . $fullfilename);
                }
                if ($withInfo) {
                    $infoData['filename'] = $fileName;
                    $infoData['fileId'] = $fileId;
                    $infoData['temporaryPath'] = $fullfilename;
                    $infoData['temporaryDisk'] = $diskName;
                    $infoData['url'] = CTemporary::getPublicUrl(static::FOLDER, $fileId);
                    $fullfilenameinf = CTemporary::publicPut(static::FOLDER_INFO, json_encode($infoData), $fileId);
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
                $fileName = carr::get($filenameArray, $k);
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);

                $this->checkExtension($ext, $allowedExtension);
                if ($validationCallback && $validationCallback instanceof Opis\Closure\SerializableClosure) {
                    $validationCallback->__invoke($fileName, $imageData);
                }

                $extension = '.' . $ext;
                $filteredData = substr($imageData, strpos($imageData, ',') + 1);
                $unencodedData = base64_decode($filteredData);
                $fileId = $this->generateFileId($extension);

                $fullfilename = CTemporary::publicPut(static::FOLDER, $unencodedData, $fileId);
                if ($withInfo) {
                    $infoData['filename'] = $fileName;
                    $infoData['fileId'] = $fileId;
                    $infoData['temporaryPath'] = $fullfilename;
                    $infoData['temporaryDisk'] = $diskName;
                    $infoData['url'] = CTemporary::getPublicUrl(static::FOLDER, $fileId);
                    $fullfilenameinf = CTemporary::publicPut(static::FOLDER_INFO, json_encode($infoData), $fileId);
                }
                $return[] = $fileId;
            }
        }
        $return = [
            'fileId' => $fileId,
            'fileName' => $fileName,
            'url' => $fileId ? CTemporary::getPublicUrl(static::FOLDER, $fileId) : '',
        ];

        return c::response()->json($return);
    }

    public function generateFileId($extension) {
        return date('Ymd') . cutils::randmd5() . 'i' . $extension;
    }
}
