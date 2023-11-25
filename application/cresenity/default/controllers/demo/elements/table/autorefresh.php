<?php

class Controller_Demo_Elements_Table_Autorefresh extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Table Auto Refresh');

        $table = $app->addTable();

        $table->setDataFromModel(Cresenity\Demo\Model\Country::class, function (CModel_Query $query) {
            $query->whereIn('continent', ['Asia', 'Europe', 'Africa']);
        });

        $table->addColumn('name')->setLabel('Name')->setCallback(function ($row, $value) {
            $icon = c::url('media/img/flags/' . cstr::tolower(carr::get($row, 'code')) . '.gif');
            $image = '<img src="' . $icon . '" />&nbsp;&nbsp;';

            return $image . $value;
        });
        $table->addColumn('continent')->setLabel('Continent')->addTransform(['uppercase']);
        $table->addColumn('code')->setLabel('Code')->setCallback(function ($row, $value) {
            return '<span class="badge badge-info">' . c::e($value) . '</span>';
        });
        $table->addColumn('code3')->setLabel('Code 3')->addTransform(function ($value) {
            return '<span class="badge badge-primary">' . c::e($value) . '</span>';
        });
        $table->addColumn('num')->setLabel('Number');
        $table->addColumn('isd')->setLabel('ISD')->setAlign('right');
        $table->addColumn('time')->setLabel('Time')->setCallback(function ($row, $value) {
            return c::now();
        })->setSearchable(false)->setSortable(false);
        $table->setAjax(true);
        $table->setOption('processing', false);
        $table->setAutoRefresh(2);

        return $app;
    }
}
