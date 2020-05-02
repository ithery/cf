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
        static::reloadTabService($tableServiceDiv, $reloadOptions);

        echo $app->render();
    }

    public static function reloadTabService($container = null, $options = array()) {
        $app = $container;
        if ($container == null) {
            $app = CApp::instance();
        }
        $daemonManager = CManager_Daemon::instance();
        $request = $options;
        if ($request == null) {
            $request = CApp_Base::getRequest();
        }
        $db = CDatabase::instance();
        $listService = $daemonManager->daemons();
        $dataService = array();
        $groupTab = carr::get($_GET, 'group');
        if ($daemonManager->haveGroup()) {
            $tabList = $app->addTabList()->setAjax(false);
            $groupKeys = $daemonManager->getGroupsKey();
            $notGrouped = $daemonManager->daemons(false);
            if (count($notGrouped) > 0) {
                $tab = $tabList->addTab()->setLabel('Not Grouped');
                static::reloadTableService($tab, ['group' => false]);
            }
            foreach ($groupKeys as $groupName) {
                $tab = $tabList->addTab()->setLabel($groupName);
                if ($groupTab == $groupName) {
                    $tab->setActive();
                }

                static::reloadTableService($tab, ['group' => $groupName]);
            }
        } else {
            $div = $app->addDiv();
            static::reloadTableService($div);
        }

        if ($container == null) {
            echo $app->render();
        }
    }

    public static function reloadTableService($container = null, $options = array()) {
        $app = $container;
        if ($container == null) {
            $app = CApp::instance();
        }
        $daemonManager = CManager_Daemon::instance();
        $request = $options;
        if ($request == null) {
            $request = CApp_Base::getRequest();
        }
        $db = CDatabase::instance();
        $group = carr::get($request, 'group');
        $listService = $daemonManager->daemons($group);
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
        $table->setApplyDataTable(false);
        $table->cellCallbackFunc(array(__CLASS__, 'cellCallback'), __FILE__);

        $table->setRowActionStyle('btn-dropdown');


        $groupQueryString = '';
        if (strlen($group) > 0) {
            $groupQueryString = '?group=' . $group;
        }

        $actMonitor = $table->addRowAction();
        $actMonitor->setIcon("fas fa-file")->setLabel('Log');
        $actMonitor->setLink(static::controllerUrl() . 'log/index/{service_class}' . $groupQueryString);
        $actStart = $table->addRowAction();
        $actStart->setIcon("fas fa-play")->setLabel('Start');
        $actStart->setLink(static::controllerUrl() . 'start/{service_class}' . $groupQueryString)->setConfirm();
        $actStop = $table->addRowAction();
        $actStop->setIcon("fas fa-stop")->setLabel('Stop');
        $actStop->setLink(static::controllerUrl() . 'stop/{service_class}' . $groupQueryString)->setConfirm();

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
        curl::redirect($this->controllerUrl() . static::groupQueryString());
    }

    public function stop($serviceClass) {
        $errCode = 0;
        $errMessage = '';
        $group = carr::get($_GET, 'group');
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

        curl::redirect($this->controllerUrl() . static::groupQueryString());
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
            case 'dump':
                return call_user_func_array([$this, 'logDump'], $logArgs);
                break;
            case 'back':
                curl::redirect($this->controllerUrl() . static::groupQueryString());
                break;
        }
    }

    public function logIndex($serviceClass = null) {
        if (strlen($serviceClass) == 0) {
            curl::redirect($this->controllerUrl());
        }
        $app = CApp::instance();
        $db = CDatabase::instance();
        $group = carr::get($_GET, 'group');
        $app->addBreadcrumb($this->getTitle(), static::controllerUrl());
        $app->title(CManager::daemon()->getServiceName($serviceClass));
        $actionContainer = $app->addDiv()->addClass('action-container mb-3');
        $restartAction = $actionContainer->addAction()->setLabel('Restart')->addClass('btn-primary')->setIcon('fas fa-sync')->setLink(static::controllerUrl() . 'log/restart/' . $serviceClass . static::groupQueryString())->setConfirm();
        $backAction = $actionContainer->addAction()->setLabel('Back')->addClass('btn-primary')->setIcon('fas fa-arrow-left')->setLink(static::controllerUrl() . static::groupQueryString());
        $rotateAction = $actionContainer->addAction()->setLabel('Dump Status')->addClass('btn-primary')->setIcon('fas fa-sync')->setLink(static::controllerUrl() . 'log/dump/' . $serviceClass . static::groupQueryString())->setConfirm();


        $logFileList = CManager::daemon()->getLogFileList($serviceClass);
        $tabList = $app->addTabList()->setAjax(true);
        $logFile = CManager::daemon()->getLogFile($serviceClass);
        $basename = basename($logFile);
        if (file_exists($logFile)) {
            $tabList->addTab()->setLabel('Current')->setAjaxUrl(static::controllerUrl() . 'log/file/' . $serviceClass . '/' . $basename);
        }
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
        $logFile = CManager::daemon()->getLogFile($serviceClass, $filename);

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
            curl::redirect($this->controllerUrl() . 'log/index' . static::groupQueryString());
        }
        $group = carr::get($_GET, 'group');
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
        CManager::daemon()->rotateLog($serviceClass);
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
        curl::redirect($this->controllerUrl() . 'log/index/' . $serviceClass . static::groupQueryString());
    }

    public function logDump($serviceClass = null) {
        $group = carr::get($_GET, 'group');
        if (strlen($serviceClass) == 0) {
            curl::redirect($this->controllerUrl() . 'log/index' . static::groupQueryString());
        }

        $app = CApp::instance();
        $db = CDatabase::instance();

        $errCode = 0;
        $errMessage = '';



        CManager::daemon()->logDump($serviceClass);

        if ($errCode == 0) {
            cmsg::add('success', 'Daemon Successfully Dumped on Log');
        } else {
            cmsg::add('error', $errMessage);
        }
        curl::redirect($this->controllerUrl() . 'log/index/' . $serviceClass . static::groupQueryString());
    }

    /**
     * 
     * @return string
     */
    private static function groupQueryString() {
        $group = carr::get($_GET, 'group');
        $groupQueryString = '';
        if (strlen($group) > 0) {
            $groupQueryString = '?group=' . $group;
        }
        return $groupQueryString;
    }

}
