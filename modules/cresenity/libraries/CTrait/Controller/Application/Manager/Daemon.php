<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_Controller_Application_Manager_Daemon {

    protected function getTitle() {
        return 'Service Manager';
    }

    public function index() {
        $app = CApp::instance();
        $db = CDatabase::instance();

        $app->title($this->getTitle());
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
            $dService['service_class'] = $kService;
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
        $actMonitor->setIcon("fas fa-file")->setLabel('Log');
        $actMonitor->setLink(static::controllerUrl() . 'log/index/{service_class}');
        $actStart = $table->addRowAction();
        $actStart->setIcon("fas fa-play")->setLabel('Start');
        $actStart->setLink(static::controllerUrl() . 'start/{service_class}')->setConfirm();
        $actStop = $table->addRowAction();
        $actStop->setIcon("fas fa-stop")->setLabel('Stop');
        $actStop->setLink(static::controllerUrl() . 'stop/{service_class}')->setConfirm();

        if ($container == null) {
            echo $app->render();
        }
    }

    public static function cellCallback($table, $col, $row, $val) {
        if ($col == 'service_status') {
            //$status = DDaemon::status(carr::get($row,'class_name'));
            $isRunning = CManager::daemon()->isRunning(carr::get($row, 'service_class'));
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
            $started = CManager::daemon()->start($serviceClass);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        if ($errCode == 0) {
            cmsg::add('success', 'Daemon Successfully Started');
        } else {
            cmsg::add('error', $errMessage);
        }
        curl::redirect($this->controllerUrl());
    }

    public function stop($serviceClass) {
        $errCode = 0;
        $errMessage = '';
        try {
            $started = CManager::daemon()->stop($serviceClass);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        if ($errCode == 0) {
            cmsg::add('success', 'Daemon Successfully Stopped');
        } else {
            cmsg::add('error', $errMessage);
        }

        curl::redirect($this->controllerUrl());
    }

    public function log() {
        $args = func_get_args();
        $method = carr::get($args, 0);
        $logArgs = array_slice($args, 1);
        switch ($method) {
            case 'index':
                return call_user_func_array([$this, 'logIndex'], $logArgs);
                break;
            case 'file':
                return call_user_func_array([$this, 'logFile'], $logArgs);
                break;
            case 'restart':
                return call_user_func_array([$this, 'logRestart'], $logArgs);
                break;
            case 'back':
                curl::redirect($this->controllerUrl());
                break;
        }
    }

    public function logIndex($serviceClass = null) {
        if (strlen($serviceClass) == 0) {
            curl::redirect($this->controllerUrl());
        }
        $app = CApp::instance();
        $db = CDatabase::instance();

        $app->addBreadcrumb($this->getTitle(), static::controllerUrl());
        $app->title(CManager::daemon()->getServiceName($serviceClass));
        $actionContainer = $app->addDiv()->addClass('action-container mb-3');
        $restartAction = $actionContainer->addAction()->setLabel('Restart')->addClass('btn-primary')->setIcon('fas fa-sync')->setLink(static::controllerUrl() . 'log/restart/' . $serviceClass)->setConfirm();


        $logFileList = CManager::daemon()->getLogFileList($serviceClass);
        $tabList = $app->addTabList()->setAjax(true);
        $logFile = CManager::daemon()->getLogFile($serviceClass);
        $basename = basename($logFile);
        $tabList->addTab()->setLabel('Current')->setAjaxUrl(static::controllerUrl() . 'log/file/' . $serviceClass . '/' . $basename);
        for ($i = 1; $i <= 10; $i++) {
            $logFileRotate = $logFile . '.' . $i;
            if (file_exists($logFileRotate)) {
                $basename = basename($logFileRotate);
                $tabList->addTab()->setLabel('Rotate:' . $i)->setAjaxUrl(static::controllerUrl() . 'log/file/' . $serviceClass . '/' . $basename);
            }
        }



        echo $app->render();
    }

    public function logFile($serviceClass = null, $filename = null) {
        
        $app = CApp::instance();
        $db = CDatabase::instance();
        $logFile = CManager::daemon()->getLogFile($serviceClass,$filename);

        $divLog = $app->addDiv()->addClass('console');
        $log = '';
        if (file_exists($logFile)) {
            $log = file_get_contents($logFile);
        }
        $divLog->add($log);
        echo $app->render();
    }

    public function logRestart($serviceClass = null) {
        if (strlen($serviceClass) == 0) {
            curl::redirect($this->controllerUrl() . 'log/index');
        }
        $app = CApp::instance();
        $db = CDatabase::instance();

        $errCode = 0;
        $errMessage = '';


        try {
            $started = CManager::daemon()->stop($serviceClass);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        sleep(2);
        try {
            $started = CManager::daemon()->start($serviceClass);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        if ($errCode == 0) {
            cmsg::add('success', 'Daemon Successfully Restarted');
        } else {
            cmsg::add('error', $errMessage);
        }
        curl::redirect($this->controllerUrl() . 'log/index/' . $serviceClass);
    }

}
