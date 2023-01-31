<?php

class Controller_Demo_Controls_Select_Search extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Select Search');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Simple Select Search');
        $selectSearch = $div->addSelectSearchControl()->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('country_id');
        $selectSearch->setSearchField('name');

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Search With Format');
        $selectSearch = $div->addSelectSearchControl()->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('country_id');
        $selectSearch->setSearchField('name');
        $selectSearch->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');

        return $app;
    }
}
