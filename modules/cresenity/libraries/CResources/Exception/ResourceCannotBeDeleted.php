<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Exception_ResourceCannotBeUpdated extends CResources_Exception {

    public static function doesNotBelongToCollection($collectionName, CApp_Model_Interface_ResourceInterface $resource) {
        return new static("Resource id {$resource->getKey()} is not part of collection `{$collectionName}`");
    }

}
