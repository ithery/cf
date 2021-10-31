<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 15, 2019, 7:15:30 PM
 */
class CModel_Activity_Observer {
    public function created(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = [];
            $table = $model->getTable();
            $tableKey = $model->getKey();
            CModel_Activity::instance()->addData($table, $tableKey, 'create', $before, $after, $changes);
        }
    }

    public function updated(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();
            $table = $model->getTable();
            $tableKey = $model->getKey();

            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }
            CModel_Activity::instance()->addData($table, $tableKey, 'update', $before, $after, $changes);
        }
    }

    public function deleted(CModel $model) {
        if (CModel_Activity::instance()->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();
            $table = $model->getTable();
            $tableKey = $model->getKey();

            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }
            CModel_Activity::instance()->addData($table, $tableKey, 'delete', $before, $after, $changes);
        }
    }
}
