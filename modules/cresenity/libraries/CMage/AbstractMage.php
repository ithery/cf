<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CMage_AbstractMage implements ArrayAccess, CMage_MageInterface {

    use CMage_Mage_Trait_AuthorizableTrait;
    use CMage_Mage_Trait_ValidationTrait;
    use CMage_Mage_Trait_FillsFieldsTrait;
    use CMage_Mage_Trait_ResolvesFieldTrait;

    public $title = null;
    public $model;

    /**
     *
     * @var CMage_Mage_FieldCollection
     */
    protected $fields;

    /**
     *
     * @var CMage_Mage_FilterData 
     */
    protected $filters;
    public $haveAdd = true;
    public $haveEdit = true;
    public $haveDelete = true;
    public $haveDetail = true;

    public function __construct() {
        $this->fields = new CMage_Mage_FieldCollection($this);
        $this->filters = new CMage_Mage_FilterData($this);
    }

    public function getTitle() {
        return $this->title;
    }

    /**
     * 
     * @return CMage_Mage_FieldData
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
        $model = $this->model;

        return new $model;
    }

}
