<?php

class Controller_Demo_Cresjs_Alpine_MasterDetail extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        c::manager()->registerModule('auto-numeric');
        $app = c::app();
        $app->title('Alpine Master Detail');
        $items = [];
        $select = new CElement_FormInput_SelectSearch();
        $select->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $select->setKeyField('id');
        $select->setSearchField('name');
        $select->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');
        // $select->setAutoSelect();
        $app->addView('demo.page.cresjs.alpine.master-detail', [
            'items' => $items,
            'selectData' => $select->buildJavascriptOptions()
        ]);

        return $app;
    }

    public function json($countryId) {
        $country = Cresenity\Demo\Model\Country::find($countryId);
        $errCode = 0;
        $errMessage = '';
        $countryData = [
            'id' => $country->id,
            'name' => $country->name,
            'price' => 100,
            'qty' => 1,
            'subtotal' => 100,
        ];

        return CApp_Base::toJsonResponse($errCode, $errMessage, $countryData);
    }
}
