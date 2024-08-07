<?php

trait CTrait_Controller_Application_Queue_Manager {
    public function manager($method = null, $arg = null) {
        if ($method == 'purge') {
            return $this->managerPurge($arg);
        }

        $app = c::app();

        $configData = c::collect(CF::config('queue.connections'));

        $tableData = $configData->filter(function ($val) {
            return !carr::get($val, 'disabled', false);
        })->mapWithKeys(function ($val, $key) {
            $driver = carr::get($val, 'driver');
            $queue = carr::get($val, 'queue');

            $status = 'ACTIVE';
            $size = '';

            try {
                $connection = CQueue::queuer()->connection($key);
                $size = $connection->size();
            } catch (Exception $ex) {
                $status = 'INACTIVE';
            }

            return [$key => [
                'connection' => $key,
                'driver' => $driver,
                'queue' => $queue,
                'status' => $status,
                'size' => $size,
            ]];
        });
        $table = $app->addTable();
        $table->setDataFromArray($tableData);
        $table->addColumn('connection')->setLabel('Name');
        $table->addColumn('driver')->setLabel('Driver');
        $table->addColumn('queue')->setLabel('Queue');
        $table->addColumn('status')->setLabel('Status')->setAlign('center')->setCallback(function ($row, $value) {
            $badgeClass = $value == 'ACTIVE' ? 'badge badge-outline-success' : 'badge badge-outline-danger';

            $val = '<span class="' . $badgeClass . '">' . $value . '</span>';

            return $val;
        });
        $table->addColumn('size')->setLabel('Size')->setAlign('right')->addTransform('formatNumber');
        $table->setApplyDataTable(false);
        $table->addRowAction()->withRowCallback(function (CElement_Component_ActionRow $element, $row) {
            $canPurges = ['database'];
            $element->setVisibility(in_array(carr::get($row, 'driver'), $canPurges));
            $element->setLabel('Purge')->addClass('btn-primary');
            $element->setLink($this->controllerUrl() . 'manager/purge/{connection}')->setConfirm();
        });

        return $app;
    }

    public function managerPurge($key) {
        $configData = c::collect(CF::config('queue.connections'));
        $queueConfig = carr::get($configData, $key);
        if ($queueConfig) {
            $connection = carr::get($queueConfig, 'connection');
            $table = carr::get($queueConfig, 'table');

            try {
                c::db($connection)->table($table)->truncate();
                c::msg('success', 'Succesfully Purge ' . $key);
            } catch (Exception $ex) {
            }
        }

        return c::redirect($this->controllerUrl());
    }
}
