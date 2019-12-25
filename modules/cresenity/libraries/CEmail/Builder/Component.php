<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component {

    protected $name;
    protected $props = [];
    protected $context = null;
    protected $defaultAttributes = [];
    protected $allowedAttributes = [];
    protected $headStyle = [];
    protected $componentHeadStyle = [];
    protected $children = [];
    protected $content = '';
    protected static $rawElement = false;

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
        $this->name = carr::get($options,'name');
        $this->rawElement = false;
       

        $attributes = array_merge($this->defaultAttributes, carr::get($options, 'globalAttributes', []), carr::get($options, 'attributes', []));
        $this->attributes = CEmail_Builder_Helper::formatAttributes($attributes, $this->allowedAttributes);
        $this->context = carr::get($options, 'context');
    }

    public function getTagName() {
        return cstr::kebabCase($this->name);
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
    public function renderChildren($options = []) {
        $childrens = $this->getChildren();
        if($childrens==null) {
            return '';
        }
       
        
        $renderer = function($component) {
            return $component->render();
        };
        $rawXML = carr::get($options, 'rawXML', false);
        $attributes = carr::get($options, 'attributes', []);
        if(isset($options['renderer'])) {
            $renderer = $options['renderer'];
        }
       
        $props = carr::get($options, 'props', []);

        if ($rawXML) {
            return carr::reduce($childrens, function($output, $child) {
                        return $output .= "\n" . Helper::jsonToXML($child->getTagName(), $child->getAttributes(), $children->getChildren(), $child->getContent());
                    },'');
        }
        $sibling = count($childrens);
        $rawComponents = carr::filter(CEmail::builder()->components(), function($c) {
            
                    return $c::isRawElement();
                });

        $nonRawSiblings = count(carr::filter($childrens,function($child)  use ($rawComponents){
            return !carr::find($rawComponents, function($c) use($child) {
                return $c::getTagName()==$child->getTagName();
            });
        }));

        

        $output = '';
        $index = 0;
        foreach ($childrens as $children) {
            $component = $children;
            if ($children instanceof CEmail_Builder_Node) {
                $options = [];
                $options['children'] = $children->getChildren();
                $options['attributes'] = array_merge($attributes, $children->getAttributes());
                $options['context'] = $this->getChildContext();
                $options['name']=$children->getComponentName();
                $options['content']=$children->getContent();
                $options['props'] = [];
                $options['props']['first'] = $index === 0;
                $options['props']['index'] = $index;
                $options['props']['last'] = $sibling - 1 === $index;
                $options['props']['sibling'] = $sibling;
                $options['props']['nonRawSiblings'] = $nonRawSiblings;
             
                $component = CEmail::Builder()->createComponent($children->getComponentName(), $options);
            }
            if ($component != null) {
                $output .= $renderer($component);
            }
            $index++;
        };



        return $output;
    }

}
