<?php

class CResources_Exception_FileCannotBeAdded_DiskDoesNotExist extends CResources_Exception_FileCannotBeAdded {
    public static function create(string $diskName): self {
        return new static("There is no filesystem disk named `{$diskName}`");
    }
}
