<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Node {

    public $parent = null;
    public $line = null;
    public $children = [];
    public $filePath = null;
    public $absoluteFilePath = null;
    public $tagName = null;
    public $attributes = [];
    public $content='';

    public function getComponentName() {
        $name = $this->tagName;

        if (cstr::startsWith($name, 'c-')) {
            $name = substr($name, '2');
        }
        return $name;
    }

    public function getChildren() {
        return $this->children;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getTagName() {
        return $this->tagName;
    }
    
    public function getContent() {
        return $this->content;
    }

}
