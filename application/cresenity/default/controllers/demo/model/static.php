<?php

class Controller_Demo_Model_Static extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Static Model');

        $table = $app->addTable();

        $table->setDataFromModel(Cresenity\Demo\Model\Country::class);
        $table->addColumn('id')->setLabel('ID');
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('continent')->setLabel('Continent');
        $table->addColumn('code')->setLabel('Code');
        $table->addColumn('code3')->setLabel('Code 3');
        $table->addColumn('num')->setLabel('Number');
        $table->addColumn('isd')->setLabel('ISD');
        $table->setAjax();

        return $app;
    }
}
