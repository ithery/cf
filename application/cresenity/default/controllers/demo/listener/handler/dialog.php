<?php

class Controller_Demo_Listener_Handler_Dialog extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Toggle Active');

        $app->addAction()->setLabel('Show Modal')->addClass('btn btn-primary')->onClickListener()->addDialogHandler()
            ->setUrl($this->controllerUrl() . 'modal');

        return $app;
    }

    public function modal($container = null, $options = []) {
        $app = $container ?: c::app();
        /** @var CApp $app */
        $request = array_merge($options, c::request()->all());

        $form = $app->addForm();
        $selectSearch = $form->addField()->setLabel('Country')
            ->addSelectSearchControl()->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('country_id');
        $selectSearch->setSearchField('name')
            ->setMultiple(true);

        return $app;
    }
}
