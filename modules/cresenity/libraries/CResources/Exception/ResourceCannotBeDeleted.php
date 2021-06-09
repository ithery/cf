<?php

class CResources_Exception_ResourceCannotBeUpdated extends CResources_Exception {
    public static function doesNotBelongToCollection($collectionName, CApp_Model_Interface_ResourceInterface $resource) {
        return new static("Resource id {$resource->getKey()} is not part of collection `{$collectionName}`");
    }
}
