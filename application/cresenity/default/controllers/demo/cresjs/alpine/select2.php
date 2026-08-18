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
        $select->setKeyField('id');
        $select->setSearchField('name');
        $select->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');
        $select->setMultiple();
        $select->setValue([1, 5]);
        // $select->setValue(2);
        $selectData = $select->buildJavascriptOptions();
        // $selectData = [];
        $app->addView('demo.page.cresjs.alpine.select2', [
            'selectData' => $selectData
        ]);

        return $app;
    }
}
