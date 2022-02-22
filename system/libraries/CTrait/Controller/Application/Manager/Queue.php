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
        $tableData = $configData->mapWithKeys(function ($val, $key) {
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

        return $app;
    }
}
