<?php

trait CTrait_Controller_Application_Manager_Queue {
    protected function getTitle() {
        return 'Service Manager';
    }

    public function index() {
        $app = CApp::instance();
        $db = CDatabase::instance();

        $app->title($this->getTitle());

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
            $element->setVisibility(in_array(carr::get($row, 'driver'), ['database']));
            $element->setLabel('Purge')->addClass('btn-primary');
            $element->setLink($this->controllerUrl() . 'purge/{connection}')->setConfirm();
        });

        return $app;
    }

    public function purge($key) {
        $configData = c::collect(CF::config('queue.connections'));
        $queueConfig = carr::get($configData, $key);
        if ($queueConfig) {
            try {
                c::db()->table('queue')->truncate();
            } catch (Exception $ex) {
            }
        }

        return c::redirect($this->controllerUrl());
    }
}
