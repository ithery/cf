<?php

class CResources_Exception_FileCannotBeAdded_FileUnacceptableForCollection extends CResources_Exception_FileCannotBeAdded {
    /**
     * Undocumented function
     *
     * @param CResources_File                    $file
     * @param CResources_ResourceCollection      $resourceCollection
     * @param CModel_HasResourceInterface|CModel $hasResource
     *
     * @return static
     */
    public static function create(CResources_File $file, CResources_ResourceCollection $resourceCollection, $hasResource) {
        $modelType = get_class($hasResource);
        return new static("The file with properties `{$file}` was not accepted into the collection named `{$resourceCollection->name}` of model `{$modelType}` with id `{$hasResource->getKey()}`");
    }
}
