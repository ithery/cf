<?php

class Controller_Demo_Elements_Table_Action extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Table Action');

        $app->addP()->add('Demo for table action, show country for continent ASIA and EUROPE only');
        $app->addP()->add('customize action table link and customize action which show just for continent not ASIA');
        $table = $app->addTable();

        $table->setDataFromModel(Cresenity\Demo\Model\Country::class, function (CModel_Query $query) {
            $query->whereIn('continent', ['Asia', 'Europe']);
        });

        $table->addColumn('name')->setLabel('Name')->setCallback(function ($row, $value) {
            $icon = c::url('media/img/flags/' . cstr::tolower(carr::get($row, 'code')) . '.gif');
            $image = '<img src="' . $icon . '" />&nbsp;&nbsp;';

            return $image . $value;
        });
        $table->addColumn('continent')->setLabel('Continent')->addTransform('uppercase');
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
                return $q->where('isd', '>', 100);
            }
            if ($keyword == 'LOW') {
                return $q->where('isd', '<=', 100);
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

    public function edit($code) {
        $app = c::app();
        $country = Cresenity\Demo\Model\Country::where('code', '=', $code)->firstOrFail();
        $form = $app->addForm();
        $widget = $form->addWidget();
        $widget->setTitle('Edit Country ' . $country->name);
        $widget->addField()->setLabel('Name')->addTextControl('name')->setValue($country->name);

        $widget->addActionList()->addAction()->setLabel('Back')->setLink(c::url('demo/elements/table/action'));

        return $app;
    }
}
