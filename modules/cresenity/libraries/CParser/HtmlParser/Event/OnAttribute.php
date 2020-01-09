<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CParser_HtmlParser_Event_OnAttribute {

    public $attributeName;
    public $attributeValue;

    public function __construct($attributeName, $attributeValue) {
        $this->attributeName = $attributeName;
        $this->attributeValue = $attributeValue;
    }

}
