<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 12:13:06 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CResources_Helpers_File as File;

class CResources_Exception_FileCannotBeAdded_FileIsTooBig extends CResources_Exception_FileCannotBeAdded {

    public static function create($path) {
        $fileSize = File::getHumanReadableSize(filesize($path));
        $maxFileSize = File::getHumanReadableSize(CF::config('resource.maxFileSize'));
        return new static("File `{$path}` has a size of {$fileSize} which is greater than the maximum allowed {$maxFileSize}");
    }

}
