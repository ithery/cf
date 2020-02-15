<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CNotification_MessageAbstract implements CNotification_MessageInterface {

    use CTrait_HasOptions;

    protected $config;

    public function __construct($config, $options) {
        $this->options = $options;
        $this->config = $config;
    }

}
