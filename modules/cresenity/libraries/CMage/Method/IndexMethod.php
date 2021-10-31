<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Method_IndexMethod extends CMage_AbstractMethod {

    public function execute() {
        $app = CApp::instance();



        $app->setTitle($this->mage->getTitle());

        if($this->mage->haveAdd) {
            $app->addAction()->setLabel('New Record')->addClass('btn btn-mage btn-primary mb-3')->setLink($this->controllerUrl().'add');
        }
        
        $table = $app->addTable();
        foreach($this->mage->fields() as $field) {
            $column = $field->addAsColumn($table);
            
        }
        
        $model = $this->mage->buildModelForIndex();
        $table->setDataFromModel($model);
        $table->setRowActionStyle('btn-dropdown');
        
        $primaryKey = $model->getKeyName();
        if($this->mage->haveEdit) {
            $action = $table->addRowAction();
            $action->setLabel('Edit')->setIcon('fas fa-edit')->setLink($this->controllerUrl().'edit/{'.$primaryKey.'}');
        }
        if($this->mage->haveDelete) {
            $action = $table->addRowAction();
            $action->setLabel('Delete')->setIcon('fas fa-trash')->setLink($this->controllerUrl().'delete/{'.$primaryKey.'}')->setConfirm();
        }
        
        echo $app->render();
    }

}
