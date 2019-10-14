<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Method_AddMethod extends CMage_AbstractMethod {

    public function execute() {
        $app = CApp::instance();

        $request = CMage::request();
        $mage = $this->mage;
        if ($request != null) {
            
            if(!$mage->authorizeToAdd()) {
                curl::redirect('/');
            }

            //$mage->validateForAdd($request);

//            $model = CDatabase::instance()->transaction(function () use ($request, $mage) {
//                list($model, $callbacks) = $mage->fill(
//                                $request, $mage->newModel()
//                );
//
//                if ($request->viaRelationship()) {
//                    $request->findParentModelOrFail()
//                            ->{$request->viaRelationship}()
//                            ->save($model);
//                } else {
//                    $model->save();
//                }
//
//                ActionEvent::forResourceCreate($request->user(), $model)->save();
//
//                collect($callbacks)->each->__invoke();
//
//                return $model;
//            });
        }

        $app->setTitle('Add ' . $this->mage->getTitle());



        $form = $app->addForm();
        foreach ($this->mage->fields() as $field) {
            if ($field->showOnAdd) {
                $fieldContainer = $field->addAsField($form);
                $control = $field->addAsControl($fieldContainer);
            }
        }


        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        echo $app->render();
    }

}
