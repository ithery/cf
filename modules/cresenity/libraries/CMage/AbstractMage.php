<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CMage_AbstractMage implements CMage_MageInterface {

    public $title = null;

    /**
     *
     * @var CMage_Mage_FieldData 
     */
    protected $fields;

    /**
     *
     * @var CMage_Mage_FilterData 
     */
    protected $filters;

    
    public $haveAdd = true;
    public $haveEdit=true;
    public $haveDelete=true;
    public $haveDetail=true;
            
    
    
    public function __construct() {
        $this->fields = new CMage_Mage_FieldData($this);
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

}
