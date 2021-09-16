<?php

class CResources_Exception_FileCannotBeAdded_UnreachableUrl extends CResources_Exception_FileCannotBeAdded {
    public static function create($url) {
        return new static("Url `{$url}` cannot be reached");
    }
}
