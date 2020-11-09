<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_Controller_Application_Server_Beanstalkd {

    protected $host;
    protected $port;

    /**
     *
     * @var CServer_Service_Beanstalkd 
     */
    protected $beanstalkd;

    public function index() {
        $app = CApp::instance();
        $app->title('Beanstalkd Status');

        $beanstalkd = $this->getBeanstalkd();

        $tubesData = $beanstalkd->getTubesStats();
        $tableData = [];

        foreach ($tubesData as $tubeData) {
            $rowData = [];

            foreach ($tubeData as $tube) {
                $rowData[carr::get($tube, 'key')] = carr::get($tube, 'value');
            }

            $tableData[] = $rowData;
        }

        
        $widget = $app->addWidget();
        $widget->setTitle('Tubes')->setNoPadding();
        //$app->addH5()->addClass('mb-3')->add('Tubes');
        $table = $widget->addTable();
        $table->setDataFromArray($tableData);
        $table->setApplyDataTable(false);
        $table->setAjax(false);




        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('Urgent')->setLabel('Urgent');
        $table->addColumn('Ready')->setLabel('Ready');
        $table->addColumn('Reserved')->setLabel('Reserved');
        $table->addColumn('Buried')->setLabel('Buried');
        $table->addColumn('Using')->setLabel('Using');
        $table->addColumn('Watching')->setLabel('Watching');
        $table->addColumn('Waiting')->setLabel('Waiting');
        $table->addColumn('Delete(cmd)')->setLabel('Delete');
        $table->addColumn('Pause')->setLabel('Pause')->setCallback(function($row, $value) {
            return carr::get($row, 'Pause(cmd)') + carr::get($row, 'Pause(sec)') + carr::get($row, 'Pause(left)');
        });
        $table->addColumn('Total')->setLabel('Total');
        $table->setRowActionStyle('btn-dropdown');
        $action = $table->addRowAction()->setLabel('Detail')->setIcon('fas fa-search');
        $action->setLink($this->controllerUrl() . 'tube/{name}');

        $action = $table->addRowAction()->setLabel('Delete Ready')->setIcon('fas fa-trash')->setConfirm();
        $action->setLink($this->controllerUrl() . 'delete/ready/{name}');

        
        $widget = $app->addWidget();
        $widget->setTitle('Stats')->setNoPadding();
        //$app->addH5()->addClass('my-3')->add('Stats');

        $statsData = $beanstalkd->getServerStats();
        $table = $widget->addTable();
        $table->setDataFromArray($statsData);
        $table->setApplyDataTable(false);
        $table->setAjax(false);

        $table->addColumn('key')->setLabel('Key');
        $table->addColumn('description')->setLabel('Description');
        $table->addColumn('value')->setLabel('Value');
        echo $app->render();
    }

    public function delete($type = null, $tube = null) {
        if ($type == null) {
            curl::redirect($this->controllerUrl());
        }
        if ($tube == null) {
            curl::redirect($this->controllerUrl());
        }
        if (!in_array($type, ['ready', 'buried', 'delayed'])) {
            curl::redirect($this->controllerUrl());
        }

        $deleteMethod = 'delete' . ucfirst($type);

        $beanstalkd = $this->getBeanstalkd();

        $beanstalkd->$deleteMethod($tube);

        cmsg::add('success', 'Successfully delete ready on tube ' . $tube);
        curl::redirect($this->controllerUrl());
    }

    public function tube($tubeName = null, $method = null, $submethod = null) {
        if ($tubeName == null) {
            curl::redirect($this->controllerUrl());
        }
        if ($method != null) {
            switch ($method) {
                case 'reload' :
                    return $this->reloadTube($tubeName, $submethod);
                    break;
            }
        }

        $app = CApp::instance();
        $app->title('Beanstalkd Tube ' . $tubeName);

        $template = $app->addTemplate()->setTemplate('CApp/Header')->setVar('title', 'Overview');

        $actions = $template->section('actions');

        $action = $actions->addAction()->setLabel('Reload')->setIcon('fas fa-sync')->addClass('btn btn-primary');
        $action->onClickListener()->addReloadHandler()->setTarget('overview-up-container')->setUrl($this->controllerUrl() . 'tube/' . $tubeName . '/reload/overview');

        $actions->addAction()->setLabel('Back')->setIcon('fa fa-arrow-left')->addClass('btn btn-primary')->setLink($this->controllerUrl());
        $divOverview = $app->addDiv('overview-up-container');

        $this->reloadTubeOverview($tubeName, $divOverview);

        $template = $app->addTemplate()->setTemplate('CApp/Header')->setVar('title', 'Next Up');
        $actions = $template->section('actions');
        $action = $actions->addAction()->setLabel('Reload')->setIcon('fas fa-sync')->addClass('btn btn-primary');
        $action->onClickListener()->addReloadHandler()->setTarget('next-up-container')->setUrl($this->controllerUrl() . 'tube/' . $tubeName . '/reload/next');

        $divNext = $app->addDiv('next-up-container');

        $this->reloadTubeNext($tubeName, $divNext);



        echo $app->render();
    }

    public function reloadTube($tubeName, $method, $container = null, $options = []) {
        switch ($method) {
            case 'next':
                return $this->reloadTubeNext($tubeName, $container, $options);

                break;
            case 'overview':
                return $this->reloadTubeOverview($tubeName, $container, $options);

                break;

            default:
                break;
        }
    }

    private function reloadTubeNext($tubeName, $container = null, $options = []) {
        $app = $container;

        if ($container == null) {
            $app = CApp::instance();
        }
        $beanstalkd = $this->getBeanstalkd();

        $nextReady = $beanstalkd->peekReady($tubeName);
        $nextBuried = $beanstalkd->peekBuried($tubeName);
        $nextDelayed = $beanstalkd->peekDelayed($tubeName);
        $divRow = $app->addDiv()->addClass('row');
        $divCol = $divRow->addDiv()->addClass('col-md-4');

        $widget = $divCol->addWidget()->setTitle('Next Ready');
        
        if ($nextReady) {
            $widget->addField()->setLabel('Job ID')->addControl('beanstalkd-next-ready-id', 'label')->setValue(carr::get($nextReady, 'id'));
            $widget->addField()->setLabel('Data')->addControl('beanstalkd-next-ready-data', 'textarea')->setValue(carr::get($nextReady, 'rawData'))->setAttr('readonly', 'readonly');
            $widget->addField()->setLabel('TTR')->addControl('beanstalkd-next-ready-ttr', 'label')->setValue(carr::get($nextReady, 'stats.ttr'));
        }
        $divCol = $divRow->addDiv()->addClass('col-md-4');

        $widget = $divCol->addWidget()->setTitle('Next Buried');
        if ($nextBuried) {
            $widget->addField()->setLabel('Job ID')->addControl('beanstalkd-next-buried-id', 'label')->setValue(carr::get($nextBuried, 'id'));
            $widget->addField()->setLabel('Data')->addControl('beanstalkd-next-buried-data', 'textarea')->setValue(carr::get($nextBuried, 'rawData'))->setAttr('readonly', 'readonly');
            $widget->addField()->setLabel('TTR')->addControl('beanstalkd-next-buried-ttr', 'label')->setValue(carr::get($nextBuried, 'stats.ttr'));
        }

        $divCol = $divRow->addDiv()->addClass('col-md-4');

        $widget = $divCol->addWidget()->setTitle('Next Delayed');
        if ($nextDelayed) {
            $widget->addField()->setLabel('Job ID')->addControl('beanstalkd-next-delayed-id', 'label')->setValue(carr::get($nextDelayed, 'id'));
            $widget->addField()->setLabel('Data')->addControl('beanstalkd-next-delayed-data', 'textarea')->setValue(carr::get($nextDelayed, 'rawData'))->setAttr('readonly', 'readonly');
            $widget->addField()->setLabel('TTR')->addControl('beanstalkd-next-delayed-ttr', 'label')->setValue(carr::get($nextDelayed, 'stats.ttr'));
        }
        if ($container == null) {

            echo $app->render();
        }
    }

    private function reloadTubeOverview($tubeName, $container = null, $options = []) {
        $app = $container;

        if ($container == null) {
            $app = CApp::instance();
        }
        $beanstalkd = $this->getBeanstalkd();
        $tubeStat = $beanstalkd->getRawTubeStats($tubeName);


        $divRow = $app->addDiv()->addClass('row');
        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $template = $divCol->addTemplate()->setTemplate('CApp/Card/Small');
        $template->setData([
            'icon' => 'fa fa-eye display-4',
            'label' => 'Connections',
            'amount' => carr::get($tubeStat, 'current-watching'),
            'description' => 'Watching this tube',
        ]);

        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $template = $divCol->addTemplate()->setTemplate('CApp/Card/Small');
        $template->setData([
            'icon' => 'fa fa-check display-4',
            'label' => 'Jobs Ready',
            'amount' => carr::get($tubeStat, 'current-jobs-ready'),
            'description' => 'Currently in the queue',
        ]);


        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $template = $divCol->addTemplate()->setTemplate('CApp/Card/Small');
        $template->setData([
            'icon' => 'fa fa-ellipsis-h display-4',
            'label' => 'Reserved Jobs',
            'amount' => carr::get($tubeStat, 'current-jobs-reserved'),
            'description' => 'Have been reserved by a worker',
        ]);

        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $template = $divCol->addTemplate()->setTemplate('CApp/Card/Small');
        $template->setData([
            'icon' => 'far fa-clock display-4',
            'label' => 'Delayed Jobs',
            'amount' => carr::get($tubeStat, 'current-jobs-delayed'),
            'description' => 'Currently in the queue',
        ]);


        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $template = $divCol->addTemplate()->setTemplate('CApp/Card/Small');
        $template->setData([
            'icon' => 'fa fa-chart-area display-4',
            'label' => 'Total Jobs',
            'amount' => carr::get($tubeStat, 'total-jobs'),
            'description' => 'Since last restart',
        ]);


        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $template = $divCol->addTemplate()->setTemplate('CApp/Card/Small');
        $template->setData([
            'icon' => 'fa fa-chart-bar display-4',
            'label' => 'Finished Jobs',
            'amount' => carr::get($tubeStat, 'cmd-delete'),
            'description' => 'Have been worked in total',
        ]);

        if ($container == null) {

            echo $app->render();
        }
    }

    private function getBeanstalkd() {
        if ($this->beanstalkd == null) {
            $this->beanstalkd = CServer::createBeanstalkd(['host' => $this->host, 'port' => $this->port]);
        }
        return $this->beanstalkd;
    }

}
