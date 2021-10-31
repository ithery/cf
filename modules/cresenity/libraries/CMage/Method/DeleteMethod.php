<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Method_DeleteMethod extends CMage_AbstractMethod {

    public function execute() {
        $app = CApp::instance();

        $request = CApp_Base::getRequestPost();

        $mage = $this->mage;



        $model = CDatabase::instance()->transaction(function () use ($request, $mage) {
            $model = $this->mage->newModel()->find($this->id);
            $model->delete();

            return $model;
        });
        curl::redirect($this->controllerUrl());
    }

}
