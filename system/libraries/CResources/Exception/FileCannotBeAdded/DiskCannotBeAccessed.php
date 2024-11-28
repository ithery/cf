<?php

class CResources_Exception_FileCannotBeAdded_DiskCannotBeAccessed extends CResources_Exception_FileCannotBeAdded {
    public static function create($diskName) {
        return new static('Disk named `' . $diskName . '` cannot be accessed');
    }
}
