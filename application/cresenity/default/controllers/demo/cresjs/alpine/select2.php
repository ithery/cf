<?php

class Controller_Demo_Cresjs_Alpine_Select2 extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Select2');
        $select = new CElement_FormInput_SelectSearch('my-select2-input-2');
        $select->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $select->setKeyField('country_id');
        $select->setSearchField('name');
        $select->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');
        $select->setAutoSelect();
        $selectData = $select->buildJavascriptOptions();
        $app->addView('demo.page.cresjs.alpine.select2', [
            'selectData' => $selectData
        ]);

        return $app;
    }
}
