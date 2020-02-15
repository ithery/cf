<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Exception_ResourceCannotBeDeleted extends CResources_Exception {

    public static function doesNotBelongToModel($resourceId, CModel $model) {
        $modelClass = get_class($model);

        return new static("Resource with id `{$resourceId}` cannot be deleted because it does not exist or does not belong to model {$modelClass} with id {$model->getKey()}");
    }

}
