<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Type_Adapter_StringAdapter extends CEmail_Builder_Type_AbstractAdapter {

    const MATCHER = '/^string/im';
    const TYPE = 'string';

    public function __construct($typeConfig, $value) {
        parent::__construct($typeConfig, $value);
        $this->matchers =  ['/.*/'];
    }

}
