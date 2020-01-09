<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Type_Adapter_IntegerAdapter extends CEmail_Builder_Type_AbstractAdapter {

    const MATCHER = '/^integer/im';
    const TYPE = 'integer';

    public function __construct($typeConfig, $value) {
        parent::__construct($typeConfig, $value);
        $this->matchers = ['/\d+/'];
    }

}
