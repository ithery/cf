<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use CEmail_Builder_Helper as Helper;

class CEmail_Builder_Component_BodyComponent_Button extends CEmail_Builder_Component_BodyComponent {

    protected $endingTag = true;
    protected $allowedAttributes = [
        'align' => 'enum(left,center,right)',
        'background-color' => 'color',
        'border-bottom' => 'string',
        'border-left' => 'string',
        'border-radius' => 'string',
        'border-right' => 'string',
        'border-top' => 'string',
        'border' => 'string',
        'color' => 'color',
        'container-background-color' => 'color',
        'font-family' => 'string',
        'font-size' => 'unit(px)',
        'font-style' => 'string',
        'font-weight' => 'string',
        'height' => 'unit(px,%)',
        'href' => 'string',
        'name' => 'string',
        'inner-padding' => 'unit(px,%){1,4}',
        'letter-spacing' => 'unitWithNegative(px,%)',
        'line-height' => 'unit(px,%,)',
        'padding-bottom' => 'unit(px,%)',
        'padding-left' => 'unit(px,%)',
        'padding-right' => 'unit(px,%)',
        'padding-top' => 'unit(px,%)',
        'padding' => 'unit(px,%){1,4}',
        'rel' => 'string',
        'target' => 'string',
        'text-decoration' => 'string',
        'text-transform' => 'string',
        'vertical-align' => 'enum(top,bottom,middle)',
        'text-align' => 'enum(left,right,center)',
        'width' => 'unit(px,%)',
    ];

}
