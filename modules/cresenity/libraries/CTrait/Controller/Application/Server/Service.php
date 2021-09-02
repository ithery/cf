
<?php

trait CTrait_Controller_Application_Server_Service {
    protected $services = ['mysql'];

    public function service($submethod = 'index') {
        if ($submethod == 'status') {
            $args = func_get_args();
            array_shift($args);
            return $this->serviceStatus(...$args);
        }
        $app = CApp::instance();

        $servicesData = c::collect($this->services)->map(function ($service) {
            $serverService = new CServer_Service_Services($service);
            return [
                'name' => $service,
                'isRunning' => $serverService->isRunning()
            ];
        })->toArray();

        $table = $app->addTable();
        $table->setDataFromArray($servicesData);
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('isRunning')->setLabel('Status')->setCallback(function ($row, $value) {
            //$status = DDaemon::status(carr::get($row,'class_name'));
            $isRunning = $value;
            $badgeClass = $isRunning ? 'badge badge-outline-success' : 'badge badge-outline-danger';
            $badgeLabel = $isRunning ? 'RUNNING' : 'STOPPED';
            $val = '<span class="' . $badgeClass . '">' . $badgeLabel . '</span>';
            return $val;
        });
        $table->setTitle('Service List');
        $table->setApplyDataTable(false);

        $table->setRowActionStyle('btn-dropdown');

        $actMonitor = $table->addRowAction();
        $actMonitor->setIcon('fas fa-file')->setLabel('Log');
        $actMonitor->setLink($this->controllerUrl() . 'service/status/{name}');
        $actMonitor->addAttr('target', '_blank');

        return $app;
    }

    public function serviceStatus($name) {
        $app = CApp::instance();
        $app->add($name);
        return $app;
    }
}
