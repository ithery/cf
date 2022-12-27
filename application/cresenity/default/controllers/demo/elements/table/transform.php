<?php

class Controller_Demo_Elements_Table_Transform extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Table Transform');

        $table = $app->addTable();

        $table->setDataFromModel(Cresenity\Demo\Model\Country::class);

        $table->addColumn('name')->setLabel('uppercase')->addTransform('uppercase');
        $table->addColumn('name')->setLabel('lowercase')->addTransform('lowercase');
        $table->addColumn('name')->setLabel('span')->addTransform('span:text-primary');
        $table->addColumn('name')->setLabel('div')->addTransform(['span:text-danger', 'div:bg-success', ]);
        //$table->addColumn('name')->setLabel('div')->addTransform(['link:' . c::url('demo/elements/table/transform/{name}'), ]);

        return $app;
    }
}
