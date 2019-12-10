<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_Controller_Application_Manager_Daemon {
    public function index() {
        $app = CApp::instance();
        $db = CDatabase::instance();

        $app->title('Service Manager');
        $actionContainer = $app->addDiv()->addClass('action-container mb-3');

        $reloadAction = $actionContainer->addAction()->setLabel('Reload')->addClass('btn-primary')->setIcon('fas fa-sync');

        $tableServiceDiv = $app->addDiv('tableService');

        $handlerActionClick = $reloadAction->addListener('click')->addHandler('reload');
        $handlerActionClick->setTarget('tableService');
        $handlerActionClick->setUrl($this->controllerUrl() . 'reloadTableService');


        $reloadOptions = array();
        $this->reloadTableService($tableServiceDiv, $reloadOptions);

        echo $app->render();
    }

    public static function reloadTableService($container = null, $options = array()) {
        $app = $container;
        if ($container == null) {
            $app = CApp::instance();
        }
        $request = $options;
        if ($request == null) {
            $request = D::getRequest();
        }
        $db = CDatabase::instance();
        $listService = CManager::getRegisteredDaemon();
        $dataService = array();
        foreach ($listService as $kService => $vService) {
            $dService = array();
            $dService['service_class'] = 'DDaemon_Service_' . $vService;
            $dService['service_name'] = $vService;
            $dataService[] = $dService;
        }
        $table = $app->addTable();
        $table->setDataFromArray($dataService);
        $table->addColumn('service_name')->setLabel('Name');
        $table->addColumn('service_status')->setLabel('Schedule');
        $table->setTitle('Service List');

        $table->cellCallbackFunc(array(__CLASS__, 'cellCallback'), __FILE__);

        $table->setRowActionStyle('btn-dropdown');


        $actMonitor = $table->addRowAction();
        $actMonitor->setIcon("fas fa-monitor")->setLabel('Monitor');
        $actMonitor->setLink(curl::base() . 'tools/service/monitor/index/{service_class}');
        $actStart = $table->addRowAction();
        $actStart->setIcon("fas fa-play")->setLabel('Start');
        $actStart->setLink(curl::base() . 'tools/service/start/{service_class}')->setConfirm();
        $actStop = $table->addRowAction();
        $actStop->setIcon("fas fa-stop")->setLabel('Stop');
        $actStop->setLink(curl::base() . 'tools/service/stop/{service_class}')->setConfirm();

        if ($container == null) {
            echo $app->render();
        }
    }

    public static function cellCallback($table, $col, $row, $val) {
        if ($col == 'service_status') {
            //$status = DDaemon::status(carr::get($row,'class_name'));
            $isRunning = DDaemon::isRunning(carr::get($row, 'service_class'));
            $badgeClass = $isRunning ? 'badge badge-outline-success' : 'badge badge-outline-danger';
            $badgeLabel = $isRunning ? 'RUNNING' : 'STOPPED';
            $val = '<span class="' . $badgeClass . '">' . $badgeLabel . '</span>';
        }
        return $val;
    }

    public function start($serviceClass) {
        $errCode = 0;
        $errMessage = '';
        try {
            $started = DDaemon::start($serviceClass);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        if ($errCode == 0) {
            cmsg::add('success', 'Daemon Successfully Started');
        } else {
            cmsg::add('error', $errMessage);
        }
        curl::redirect('tools/service');
    }

    public function stop($serviceClass) {
        $errCode = 0;
        $errMessage = '';
        try {
            $started = DDaemon::stop($serviceClass);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        if ($errCode == 0) {
            cmsg::add('success', 'Daemon Successfully Stopped');
        } else {
            cmsg::add('error', $errMessage);
        }

        curl::redirect('tools/service');
    }

  

}