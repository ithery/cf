<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder {

    protected $components;
    protected static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CEmail_Builder();
        }
        return static::$instance;
    }

    public function __construct() {
        $this->registerComponent(CEmail_Builder_Component_BodyComponent_Body::class);
        $this->registerComponent(CEmail_Builder_Component_BodyComponent_Section::class);
        $this->registerComponent(CEmail_Builder_Component_BodyComponent_Column::class);
        $this->registerComponent(CEmail_Builder_Component_BodyComponent_Text::class);
    }

    public function registerComponent($componentClass) {
        $name = carr::last(explode("_",$componentClass));
        $this->components[cstr::kebabCase($name)] = $componentClass;
    }

    public function createComponent($name, $options = []) {
        $componentClass = carr::get($this->components, $name);
        if ($componentClass) {
            $component = new $componentClass($options);
            if ($component->hasHeadStyle()) {
                $component->context->addHeadStyle($name, $component->getHeadStyle());
            }
            if ($component->hasComponentHeadStyle()) {
                $component->context->addComponentHeadStyle($name, $component->getComponentHeadStyle());
            }
            return $component;
        }
        //throw new Exception('component not found:'.$name);
        return null;
    }

    public function components() {
        return $this->components;
    }
    
    public function globalData() {
        return CEmail_Builder_GlobalData::instance();
    }
    
    public function determineTypeAdapter($typeConfig) {
        return CEmail_Builder_Type_TypeFactory::getAdapter($typeConfig);
    }

    public function toHtml($xml, $options = []) {
        $parser = new CEmail_Builder_Parser($xml, $options = []);
        return $parser->parse();
    }

}
