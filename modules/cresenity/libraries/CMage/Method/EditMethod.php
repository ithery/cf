<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CMage_Method_EditMethod extends CMage_AbstractMethod {

    public function execute() {
        $app = CApp::instance();

        $request = CApp_Base::getRequestPost();
       
        $mage = $this->mage;
       
        if ($request!=null) {
            
//            if(!$mage->authorizeToAdd()) {
//                curl::redirect('/');
//            }

            //$mage->validateForAdd($request);

            $model = CDatabase::instance()->transaction(function () use ($request, $mage) {
                $addFields = $this->mage->getEditFieldsFromRequest();
                $model = $addFields->fillModelFromRequest($this->mage->newModel()->find($this->id),$request);
//                list($model, $callbacks) = $mage->fill(
//                                $request, $mage->newModel()
//                );

//                if ($request->viaRelationship()) {
//                    $request->findParentModelOrFail()
//                            ->{$request->viaRelationship}()
//                            ->save($model);
//                } else {
//                    $model->save();
//                }
                $model->save();
                //ActionEvent::forResourceCreate($request->user(), $model)->save();

                //collect($callbacks)->each->__invoke();

                return $model;
            });
            curl::redirect($this->controllerUrl());
        }

        $app->setTitle('Add ' . $this->mage->getTitle());



        $form = $app->addForm();
        $currentModel = $this->mage->newModel()->find($this->id);
        foreach ($this->mage->fields() as $field) {
            if ($field->showOnAdd) {
                $controlValue = carr::get($request,$field->getName(),$currentModel->{$field->getName()});
                $fieldContainer = $field->addAsField($form);
                $control = $field->addAsControl($fieldContainer);
                $control->setValue($controlValue);
            }
        }


        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        echo $app->render();
    }

}
