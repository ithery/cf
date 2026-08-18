<?php

class Controller_Demo_Elements_Table_Infinite extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Table Cell');

        $table = $app->addTable();

        $table->setDataFromModel(Cresenity\Demo\Model\Country::class, function (CModel_Query $query) {
            $query->orderBy('name');
        });
        $table->setRowClassCallback(function (Cresenity\Demo\Model\Country $row) {
            if (cstr::lower($row->continent) == 'asia') {
                return 'font-weight-bold';
            }

            return '';
        });
        $table->addColumn('name')->setLabel('Name')->setCallback(function ($row, $value) {
            $icon = c::url('media/img/flags/' . cstr::tolower(carr::get($row, 'code')) . '.gif');
            $image = '<img src="' . $icon . '" />&nbsp;&nbsp;';

            return $image . $value;
        })->setWidth(200);
        $table->addColumn('continent')->setLabel('Information')->setCallback(function (Cresenity\Demo\Model\Country $row, $value) {
            $d = c::div();
            $dContinent = $d->addDiv()->addClass('mb-1');
            $dContinent->addDiv()->addClass('text-muted')->add('Continent');
            $dContinent->addDiv()->add($row->continent);
            $dCode = $d->addDiv()->addClass('mb-1');
            $dCode->addDiv()->addClass('text-muted')->add('Code');
            $dCode->addDiv()->add($row->code);
            $dCode3 = $d->addDiv()->addClass('mb-1');
            $dCode3->addDiv()->addClass('text-muted')->add('Code 3');
            $dCode3->addDiv()->add($row->code3);

            return $d;
        });
        $table->addColumn('code')->setLabel('Code')->setVisible(false); //make it hidden to enable searching
        $table->addColumn('code3')->setLabel('Code 3')->setVisible(false); //make it hidden to enable searching
        $table->addColumn('num')->setLabel('Other Information')->setCallback(function (Cresenity\Demo\Model\Country $row, $value) {
            $d = c::div();
            $dNumber = $d->addDiv()->addClass('mb-1');
            $dNumber->addDiv()->addClass('text-muted')->add('Number');
            $dNumber->addDiv()->add($row->num);
            $dIsd = $d->addDiv()->addClass('mb-1');
            $dIsd->addDiv()->addClass('text-muted')->add('ISD');
            $dIsd->addDiv()->add($row->isd);

            return $d;
        });
        $table->addColumn('isd')->setLabel('ISD')->setVisible(false);

        $table->setAjax();

        return $app;
    }
}
