<?php

defined('SYSPATH') or die('No direct access allowed.');

use CResources_Helpers_File as File;

class CResources_Exception_FileCannotBeAdded_FileIsTooBig extends CResources_Exception_FileCannotBeAdded {
    public static function create($path) {
        $fileSize = File::getHumanReadableSize(filesize($path));
        $maxFileSize = File::getHumanReadableSize(CF::config('resource.max_file_size'));

        return new static('File `' . $path . '` has a size of ' . $fileSize . ' which is greater than the maximum allowed ' . $maxFileSize . '');
    }
}
