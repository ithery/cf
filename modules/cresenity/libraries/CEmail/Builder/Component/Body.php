<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Element_Body extends CEmail_Element {

    protected $allowedAttributes = array(
        'width' => 'unit(px,%)',
        'background-color' => 'color',
    );
    protected $defaultAttributes = array(
        'width' => '600px',
    );

}
