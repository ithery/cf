<?php

class CResources_Exception_FileCannotBeAdded_RequestDoesNotHaveFile extends CResources_Exception_FileCannotBeAdded {
    public static function create(string $key): self {
        return new static("The current request does not have a file in a key named `{$key}`");
    }
}
