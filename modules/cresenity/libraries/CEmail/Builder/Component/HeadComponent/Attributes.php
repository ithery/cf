<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component_HeadComponent_Attributes extends CEmail_Builder_Component_HeadComponent {

    protected static $tagName = 'c-attributes';

    public function handler() {
        $childrens = $this->getChildren();
        foreach ($childrens as $child) {

            $children = $child->getChildren();
            $tagName = $child->getTagName();
            $attributes = $child->getAttributes();
            if ($tagName === 'c-class') {
                $this->context->addHead('classes', carr::get($attributes, 'name'), carr::except($attributes, ['name']));
                $classesDefaultParam = carr::reduce($children, function($acc, $child) {
                            return $acc[$child->getTagName()] = $child->getAttributes();
                        }, []);
                $this->context->addHead('classesDefault', carr::get($attributes, 'name'), $classesDefaultParam);
            } else {
                $this->context->addHead('defaultAttributes', $tagName, $attributes);
            }
        }
        
    }

}
