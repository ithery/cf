<?php

trait CTrait_Controller_Application_OAuth_Client {
    protected function getTitle() {
        return 'OAuth Client';
    }

    protected function getApiGroup() {
        return 'api';
    }

    public function index() {
        $app = c::app();
        $app->title($this->getTitle());

        $oauth = CApi::oauth($this->getApiGroup());
        $table = $app->addTable();
        $table->setDataFromModel($oauth->clientModel(), function (CModel_Query $q) {
            $q->orderBy('created', 'desc');
        });
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('secret')->setLabel('Secret');
        $table->addColumn('provider')->setLabel('Provider');
        $table->addColumn('redirect')->setLabel('Redirect');
        $table->addColumn('personal_access_client')->setLabel('Personal Access Client')->addTransform('yesNo');
        $table->addColumn('password_client')->setLabel('Password Client')->addTransform('yesNo');
        $table->setAjax(true);

        return $app;
    }
}
