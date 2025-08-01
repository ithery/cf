<?php

class CAjax_Info {
    public static function getFileInfo($fileId) {
        $path = CTemporary::getPath(CAjax_Engine_FileUpload::FOLDER_INFO, $fileId);
        $info = CTemporary::disk()->get($path);

        return json_decode($info, true);
    }

    public static function getImageInfo($fileId) {
        $path = CTemporary::getPath(CAjax_Engine_ImgUpload::FOLDER_INFO, $fileId);
        $info = CTemporary::disk()->get($path);

        return json_decode($info, true);
    }
}
