<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 5:16:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_HasSlug_Observer {

    public function creating(CModel $model) {
//        $model->generateSlugOnCreate();
    }

    public function updating(CModel $model) {
//        $model->generateSlugOnUpdate();
    }

    public function validating(CModel $model) {

        if ($model->exists && $model->getSlugOptions()->generateSlugsOnUpdate) {
            $model->generateSlugOnUpdate();
        } elseif (!$model->exists && $model->getSlugOptions()->generateSlugsOnCreate) {
            $model->generateSlugOnCreate();
        }
    }

}
