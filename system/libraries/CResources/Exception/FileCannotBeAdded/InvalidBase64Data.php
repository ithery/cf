<?php

class CResources_Exception_FileCannotBeAdded_InvalidBase64Data extends CResources_Exception_FileCannotBeAdded {
    public static function create() {
        return new static('Invalid base64 data provided');
    }
}
