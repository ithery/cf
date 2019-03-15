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
            $table = $model->getTable();
            $key = $model->getKey();
            CModel_Activity::instance()->addData($table,$key,'create', $before, $after, $changes);
        }
    }

    public function updated(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();
            $table = $model->getTable();
            $key = $model->getKey();
            
            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }
            CModel_Activity::instance()->addData($table,$key,'update', $before, $after, $changes);
        }
    }

    public function deleted(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();
            $table = $model->getTable();
            $key = $model->getKey();

            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }
            CModel_Activity::instance()->addData($table,$key,'delete', $before, $after, $changes);
        }
    }

}
