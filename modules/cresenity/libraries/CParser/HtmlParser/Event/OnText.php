<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CParser_HtmlParser_Event_OnText {

    public $value;

    public function __construct($value) {
        $this->value = $value;
    }

}
