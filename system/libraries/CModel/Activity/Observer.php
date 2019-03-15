<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 15, 2019, 7:15:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Activity_Observer {

    public function created(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {

            $before = [];
            $after = $model->getAttributes();
            $changes = [];
            CModel_Activity::instance()->dispatch('OnActivity', 'create', $before, $after, $changes);
        }
    }

    public function updated(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();

            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }
            CModel_Activity::instance()->dispatch('OnActivity', 'update', $before, $after, $changes);
        }
    }

    public function deleted(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {

            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();

            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }
            CModel_Activity::instance()->dispatch('OnActivity', 'delete', $before, $after, $changes);
        }
    }

}
