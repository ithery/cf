<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component {
    protected $childs = [];
    protected $attrs = [];
    protected $name;
    protected $content;
    protected $rawElement = false;
    protected $defaultAttributes = [];
    protected $allowedAttributes = null;

    public function __construct() {
        
    }

    public function getTagName() {
        return cstr::kebabCase($this->name);
    }

    public function isRawElement() {
        return !!$this->rawElement;
    }

    public function add($element) {
        $rawElement = $element;
        if ($rawElement instanceof CEmail_Element) {
            $this->childs[] = $rawElement;
        } else {
            $rawElement = new CEmail_Element_Raw();
            $rawElement->setContent($element);
            $this->childs[] = $rawElement;
        }
        return $rawElement;
    }

    public function addBody() {
        $element = new CEmail_Element_Body();
        $this->add($element);
        return $element;
    }

    public function addSection() {
        $element = new CEmail_Element_Section();
        $this->add($element);
        return $element;
    }

    public function addColumn() {
        $element = new CEmail_Element_Column();
        $this->add($element);
        return $element;
    }

    public function addImage() {
        $element = new CEmail_Element_Image();
        $this->add($element);
        return $element;
    }

    public function setAttr($key, $value) {
        $this->attrs[$key] = $value;
        return $this;
    }

}