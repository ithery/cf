<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component {

    protected $name;
    protected $props = [];

    /**
     *
     * @var CEmail_Builder_Context
     */
    protected $context = null;
    protected $defaultAttributes = [];
    protected $allowedAttributes = [];
    protected $headStyle = [];
    protected $componentHeadStyle = [];
    protected $children = [];
    protected $content = '';
    protected static $rawElement = false;
    protected static $endingTag = false;
    protected static $tagName = '';

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
        $this->children = carr::get($options, 'children', []);
        $this->content = carr::get($options, 'content', '');
        $this->name = carr::get($options, 'name');

        $globalAttributes = CEmail::builder()->globalData()->get('defaultAttributes');
        
        //$attributes = array_merge($this->defaultAttributes, carr::get($options, 'globalAttributes', []), carr::get($options, 'attributes', []));
        $attributes = array_merge($this->defaultAttributes, $globalAttributes, carr::get($options, 'attributes', []));
        $this->attributes = CEmail_Builder_Helper::formatAttributes($attributes, $this->allowedAttributes);
        $this->context = carr::get($options, 'context');
    }

    public static function getTagName() {
        return static::$tagName;
    }
    public static function isEndingTag() {
        return static::$endingTag;
    }

    public static function isRawElement() {
        return !!static::$rawElement;
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
        return trim($this->content);
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

    public function getHeadStyle() {
        return $this->headStyle;
    }

    public function getComponentHeadStyle() {
        return $this->componentHeadStyle;
    }

    public function hasHeadStyle() {
        return count($this->headStyle) > 0;
    }

    public function hasComponentHeadStyle() {
        return count($this->componentHeadStyle) > 0;
    }

    public function getProp($key, $defaultValue = null) {
        return carr::get($this->props, $key, $defaultValue);
    }

    public function getChildren() {
        return $this->children;
    }

   
}
