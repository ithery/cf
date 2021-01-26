<?php

class CResources_Exception_FileCannotBeAdded_UnknownType extends CResources_Exception_FileCannotBeAdded {
    public static function create() {
        return new static('Only strings, FileObjects and UploadedFileObjects can be imported');
    }
}
