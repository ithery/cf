<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Helpers_TemporaryDirectory {

    const DEFAULT_FOLDER = 'resource';

    protected static function folder() {
        $folder = CF::config('resource.temporary_directory_path');
        if (strlen($folder) == 0) {
            $folder = static::DEFAULT_FOLDER;
        }
        return $folder;
    }

    public static function generateLocalFilePath($extension = null) {

        $filename = CTemporary::generateRandomFilename();
        if ($extension != null) {
            $filename .= '.' . $extension;
        }
        $localPath = CTemporary::getLocalPath(static::folder(), $filename);
        $dirPath = dirname($localPath);
        if (!is_dir($dirPath)) {
            @mkdir($dirPath, 0777, true);
        }
        return $localPath;
    }

    public static function delete($filename) {
        $filename = basename($filename);
        return CTemporary::deleteLocal(static::folder(), $filename);
    }

}
