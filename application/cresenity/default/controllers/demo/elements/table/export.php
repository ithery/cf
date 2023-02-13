<?php

class Controller_Demo_Elements_Table_Export extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Table Export Data');
        $isExport = (bool) c::request()->export;

        $app->addP()->add('Demo for table Export who use data from model setDataFromModel()');

        // widget is optional, use for wrapper of button
        $widget = $app->addWidget()->setTitle('Widget Function')->addClass('mb-2');

        // button export
        $widget->addA()->add('Export Data')->addClass('btn btn-info text-white')->setAttr('target', '_blank')->setAttr('href', c::url('demo/elements/table/export/index?export=1'));

        $table = $app->addTable();
        $table->setDataFromModel(Cresenity\Demo\Model\Item::class, function (CModel_Query $query) {
            $query->with('category');
        });
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('category.name')->setLabel('Category')->setWidth('200')->setExportCallback(function ($row, $value) {
            return 'category Name : ' . $value;
        });
        $table->setAjax(false);

        // export table here
        if ($isExport) {
            $table->downloadexcel('export-data-' . date('Ymdhis') . '.xls');
        }

        return $app;
    }
}
