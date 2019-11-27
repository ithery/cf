<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 12:39:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use Finfo;

class CResources_Helpers_File {

    public static function renameInDirectory($fileNameWithDirectory, $newFileNameWithoutDirectory) {
        $targetFile = pathinfo($fileNameWithDirectory, PATHINFO_DIRNAME) . '/' . $newFileNameWithoutDirectory;
        rename($fileNameWithDirectory, $targetFile);
        return $targetFile;
    }

    public static function getHumanReadableSize($sizeInBytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        if ($sizeInBytes == 0) {
            return '0 ' . $units[1];
        }
        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }
        return round($sizeInBytes, 2) . ' ' . $units[$i];
    }

    public static function getMimetype($path) {
        $finfo = new Finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($path);
    }

}
