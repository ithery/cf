<?php

trait CTrait_Controller_Application_Queue_Batch {
    public function batch() {
        $app = c::app();
        $queueBatchModelName = null;
        if (property_exists($this, 'queueBatchModelName')) {
            $queueBatchModelName = $this->queueBatchModelName;
        }

        if (!$queueBatchModelName) {
            $app->addAlert()->add('Please set model queue batch through protected $queueBatchModelName');

            return $app;
        }
        $table = $app->addTable();
        $table->setDataFromModel($queueBatchModelName);
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('total_jobs')->setLabel('Total');
        $table->addColumn('pending_jobs')->setLabel('Pending');
        $table->addColumn('failed_jobs')->setLabel('Failed');
        $table->setAjax();

        return $app;
    }
}
