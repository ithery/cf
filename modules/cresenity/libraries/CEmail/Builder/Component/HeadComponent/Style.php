<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component_HeadComponent_Style extends CEmail_Builder_Component_HeadComponent {

    protected static $tagName = 'c-style';
    protected static $endingTag = true;
    protected $allowedAttributes = [
        'inline' => 'string',
    ];

    public function handler() {
        $attr = $this->getAttribute('inline') === 'inline' ? 'inlineStyle' : 'style';

        $this->context->addHead($attr, $this->getContent());
    }

}
