<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component {
    protected $name;
    protected $props = [];
    protected $context = [];
    protected $defaultAttributes = [];
    protected $allowedAttributes = [];

    public function __construct($options) {
        $defaultOptions = array();
        $defaultOptions['attributes'] = [];
        $defaultOptions['children'] = [];
        $defaultOptions['content'] = '';
        $defaultOptions['context'] = [];
        $defaultOptions['props'] = [];
        $defaultOptions['globalAttributes'] = [];
        $options = array_merge($defaultOptions, $options);

        $this->props = carr::get($options, 'props');
        $this->props['children'] = carr::get($options, 'children');
        $this->props['content'] = carr::get($options, 'content');


        $attributes = array_merge($this->defaultAttributes, carr::get($options, 'globalAttributes', []), carr::get($options, 'attributes', []));
        $this->attributes = CEmail_Builder_Helper::formatAttributes($attributes, $this->allowedAttributes);
        $this->context = carr::get($options, 'context');
    }

    public function getTagName() {
        return cstr::kebabCase($this->name);
    }

    public function isRawElement() {
        return !!$this->rawElement;
    }

    public function getChildContext() {
        return $this->context;
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

    public function getAttribute($name) {
        return carr::get($this->attributes, $name);
    }
    

    public function getContent() {
        return trim(carr::get($this->props, 'content'));
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
