<?php

class Controller_Demo_Model_Remote extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Remote Model');

        $table = $app->addTable('dummytable');

        $table->setDataFromModel(Cresenity\Demo\Model\Dummy::class, function ($q) {
        });
        $table->addColumn('userId')->setLabel('userId');
        $table->addColumn('id')->setLabel('ID');
        $table->addColumn('title')->setLabel('Title');
        $table->addColumn('completed')->setLabel('Completed');
        $table->setAjax();

        return $app;
    }
}
