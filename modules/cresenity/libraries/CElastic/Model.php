<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElastic_Model extends CModel {

    use CElastic_Trait_Searchable;

    protected $elastic;

    public function __construct(array $attributes = []) {
        $this->elastic = CElastic::instance();
        parent::__construct($attributes);
    }

}
