<?php

class Controller_Demo_Elements_Table_View extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('From View');

        $view = $app->addView('demo.page.elements.table.view');
        $section = $view->viewElement('table');

        $table = $section->addTable('table_country');
        $table->setDataFromModel(Cresenity\Demo\Model\Country::class, function (CModel_Query $query) {
            $query->whereIn('continent', ['Asia', 'Europe']);
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
        $table->addColumn('isd')->setLabel('ISD H/L')->setCallback(function ($row, $value) {
            return $value > 100 ? 'HIGH' : 'LOW';
        })->setSearchCallback(function ($q, $keyword) {
            if ($keyword == 'HIGH') {
                $q->where('isd', '>', 100);
            }
            if ($keyword == 'LOW') {
                $q->where('isd', '<=', 100);
            }
        });
        $table->setAjax(true);
        $table->setRowActionStyle('btn-dropdown');
        $table->addRowAction()->setLabel('Edit')
            ->setLink(c::url('demo/elements/table/action/edit/{code}'));
        $table->addRowAction()->setLabel('Set To Asia')
            ->setLink(c::url('demo/elements/table/action/index'))
            ->withRowCallback(function (CElement_Component_ActionRow $element, Cresenity\Demo\Model\Country $model) {
                $element->setVisibility(cstr::toupper($model->continent) != 'ASIA');
            });

        return $app;
    }
}
