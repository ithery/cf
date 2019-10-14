<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CMage_AbstractMage implements ArrayAccess, CMage_MageInterface {

    use CMage_Mage_Trait_AuthorizableTrait;
    use CMage_Mage_Trait_ValidationTrait;
    use CMage_Mage_Trait_LoadAttributeTrait;
    use CMage_Mage_Trait_FillsFieldsTrait;
    use CMage_Mage_Trait_ResolvesFieldTrait;
    use CMage_Mage_Trait_DelegatesToModelTrait;

    public $title = null;
    public $modelClass;
    public $model;

    /**
     *
     * @var CMage_Mage_FieldCollection
     */
    protected $fields;

    /**
     *
     * @var CMage_Mage_FilterCollection
     */
    protected $filters;
    public $haveAdd = true;
    public $haveEdit = true;
    public $haveDelete = true;
    public $haveDetail = true;

    public function __construct() {
        $this->fields = new CMage_Mage_FieldCollection([], $this);
        $this->filters = new CMage_Mage_FilterCollection([], $this);
    }

    public function getTitle() {
        return $this->title;
    }

    /**
     * 
     * @return CMage_Mage_FieldCollection
     */
    public function fields() {
        return $this->fields;
    }

    /**
     * 
     * @return CMage_Mage_FieldData
     */
    public function filters() {
        return $this->filters;
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public function newModel() {
        $model = $this->modelClass;
       
        return new $model;
    }

    public function setModel($model) {
        $this->model = $model;
        return $this;
    }

    public function getAddFieldsFromRequest($request=array()) {
         if($request==null){
            $request = CApp_Base::getRequest();
        }
        $addFields = $this->fields->reject(function($field) use ($request) {
            return !$field->showOnAdd || !array_key_exists($field->getName(), $request);
        });
        return $addFields;
    }
    public function getEditFieldsFromRequest($request=array()) {
        if($request==null){
            $request = CApp_Base::getRequest();
        }
        $addFields = $this->fields->reject(function($field) use ($request) {
            return !$field->showOnEdit || !array_key_exists($field->getName(), $request);
        });
        return $addFields;
    }
    public function getIndexFields() {
        $request = CApp_Base::getRequest();
        $indexFields = $this->fields->reject(function($field) {
            return !$field->showOnIndex; 
        });
        return $indexFields;
    }
    
    public function buildModelForIndex() {
        $model = $this->newModel();
        $indexFields = $this->getIndexFields();
        
        foreach($indexFields as $field) {
            $model->addSelect($field->getName());
        }
        return $model;
    }

}
