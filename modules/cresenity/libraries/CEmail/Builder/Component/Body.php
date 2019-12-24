<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component_Body extends CEmail_Builder_Component {

    protected $allowedAttributes = array(
        'width' => 'unit(px,%)',
        'background-color' => 'color',
    );
    protected $defaultAttributes = array(
        'width' => '600px',
    );

}
