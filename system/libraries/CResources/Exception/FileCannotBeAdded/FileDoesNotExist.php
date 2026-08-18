<?php

defined('SYSPATH') or die('No direct access allowed.');

class CResources_Exception_FileCannotBeAdded_FileDoesNotExist extends CResources_Exception_FileCannotBeAdded {
    public static function create($path) {
        return new static('File `' . $path . '` does not exist');
    }
}
