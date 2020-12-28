<?php

class CResources_Exception_FileCannotBeAdded_FileUnacceptableForCollection extends CResources_Exception_FileCannotBeAdded {
    public static function create(CResources_File $file, CResources_ResourceCollection $resourceCollection, CModel_HasResourceInterface $hasResource) {
        $modelType = get_class($hasResource);
        return new static("The file with properties `{$file}` was not accepted into the collection named `{$resourceCollection->name}` of model `{$modelType}` with id `{$hasResource->getKey()}`");
    }
}
