<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_Controller_Application_Server_Beanstalkd {

    protected $host;
    protected $port;

    public function index() {
        $app = CApp::instance();
        $app->title('Beanstalkd Status');

        $beanstalkd = CServer::createBeanstalkd(['host' => $this->host, 'port' => $this->port]);

        $tubesData = $beanstalkd->getTubesStats();
        $tableData = [];

        foreach ($tubesData as $tubeData) {
            $rowData = [];

            foreach ($tubeData as $tube) {
                $rowData[carr::get($tube, 'key')] = carr::get($tube, 'value');
            }

            $tableData[] = $rowData;
        }

        $app->addH5()->addClass('mb-3')->add('Tubes');
        $table = $app->addTable();
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
        $table->addColumn('Pause')->setLabel('Pause')->setCallback(function($row,$value) {
            return carr::get($row,'Pause(cmd)') + carr::get($row,'Pause(sec)') + carr::get($row,'Pause(left)');
            
        });
        $table->addColumn('Total')->setLabel('Total');
        $table->setRowActionStyle('btn-dropdown');
        $action = $table->addRowAction()->setLabel('Delete Ready')->setIcon('fas fa-trash')->setConfirm();
        $action->setLink($this->controllerUrl() . 'delete/ready/{name}');

        $app->addH5()->addClass('my-3')->add('Stats');

        $statsData = $beanstalkd->getServerStats();
        $table = $app->addTable();
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

        $beanstalkd = CServer::createBeanstalkd(['host' => $this->host, 'port' => $this->port]);

        $beanstalkd->$deleteMethod($tube);

        cmsg::add('success', 'Successfully delete ready on tube ' . $tube);
        curl::redirect($this->controllerUrl());
    }

}
