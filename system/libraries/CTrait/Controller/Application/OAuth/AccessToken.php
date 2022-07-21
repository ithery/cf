<?php

trait CTrait_Controller_Application_OAuth_AccessToken {
    protected function getTitle() {
        return 'OAuth Access Token';
    }

    protected function getApiGroup() {
        return 'api';
    }

    public function index() {
        $app = c::app();
        $app->title($this->getTitle());

        $oauth = CApi::oauth($this->getApiGroup());
        $table = $app->addTable();
        $table->setDataFromModel($oauth->tokenModel(), function (CModel_Query $q) {
            $q->with(['oauthClient']);
            $q->whereHas('oauthClient');

            $q->orderBy('created', 'desc');
        });
        $table->addColumn('oauthClient.name')->setLabel('Client');
        $table->addColumn('user_type')->setLabel('User Type');
        $table->addColumn('user_id')->setLabel('User ID');
        $table->addColumn('token')->setLabel('Token')->addTransform('showMore:10');
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('scopes')->setLabel('Scopes');
        $table->addColumn('revoked')->setLabel('Revoked')->addTransform('yesNo');
        $table->addColumn('expires_at')->setLabel('Expired')->addTransform('formatDatetime');
        $table->addColumn('created')->setLabel('Created')->addTransform('formatDatetime');
        $table->setAjax(true);

        return $app;
    }
}
