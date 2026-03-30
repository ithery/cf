<?php

defined('SYSPATH') or die('No direct access allowed.');

class CModel_HasSlug_Observer {
    public function creating(CModel $model) {
        //$model->generateSlugOnCreate();
    }

    public function updating(CModel $model) {
        //$model->generateSlugOnUpdate();
    }

    public function validating(CModel $model) {
        if ($model->exists && $model->getSlugOptions()->generateSlugsOnUpdate) {
            $model->generateSlugOnUpdate();
        } elseif (!$model->exists && $model->getSlugOptions()->generateSlugsOnCreate) {
            $model->generateSlugOnCreate();
        }
    }
}
