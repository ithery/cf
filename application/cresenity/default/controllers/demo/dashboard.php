<?php
use Cresenity\Demo\Model\Dummy as DummyModel;
use Cresenity\Demo\Model\Country as CountryModel;

class Controller_Demo_Dashboard extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Dashboard');

        // $table = $app->addTable();

        // $table->setDataFromModel(CountryModel::class);
        // $table->addColumn('id')->setLabel('ID');
        // $table->addColumn('name')->setLabel('Name');
        // $table->addColumn('continent')->setLabel('Continent');
        // $table->addColumn('code')->setLabel('Code');
        // $table->addColumn('code3')->setLabel('Code 3');
        // $table->addColumn('num')->setLabel('Number');
        // $table->addColumn('isd')->setLabel('ISD');
        // $table->setAjax();

        $table = $app->addTable('dummytable');

        $table->setDataFromModel(DummyModel::class, function ($q) {
        });
        $table->addColumn('userId')->setLabel('userId');
        $table->addColumn('id')->setLabel('ID');
        $table->addColumn('title')->setLabel('Title');
        $table->addColumn('completed')->setLabel('Completed');
        $table->setAjax();

        return $app;
    }
}
