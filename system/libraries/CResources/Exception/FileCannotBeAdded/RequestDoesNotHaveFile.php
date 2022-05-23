<?php

class CResources_Exception_FileCannotBeAdded_RequestDoesNotHaveFile extends CResources_Exception_FileCannotBeAdded {
    public static function create($key) {
        return new static("The current request does not have a file in a key named `{$key}`");
    }
}
