<?php

abstract class CApi_Session_DriverAbstract {
    protected $group;

    public function __construct($options) {
        $this->group = carr::get($options, 'group');
    }
}
