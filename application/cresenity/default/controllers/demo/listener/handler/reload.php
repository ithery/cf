<?php

class Controller_Demo_Listener_Handler_Reload extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Reload');

        $action = $app->addAction()->setLabel('Reload')->addClass('btn btn-primary');

        $divTarget = $app->addDiv();
        $action->onClickListener()->addReloadHandler()
            ->setUrl($this->controllerUrl() . 'reload')
            ->setTarget($divTarget);

        return $app;
    }

    public function reload($container = null, $options = []) {
        $app = $container ?: c::app();
        /** @var CApp $app */
        $request = array_merge($options, c::request()->all());

        $form = $app->addForm();
        $selectSearch = $form->addField()->setLabel('Country')
            ->addSelectSearchControl()->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('country_id');
        $selectSearch->setSearchField('name')
            ->setMultiple(true);
        $autoNumeric = $form->addField()->setLabel('Price')->addAutoNumericControl()->setValue(30000);

        return $app;
    }
}
