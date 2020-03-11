<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CEmail_Builder_Type_AbstractAdapter {

    const MATCHER = '';
    const TYPE = 'unknown';

    protected $value;
    protected $typeConfig;
    protected $matchers = [];
    protected $errorMessage = '';

    public function __construct($typeConfig, $value) {
        $this->value = $value;
        $this->typeConfig = $typeConfig;
        $this->matchers = [];
        $this->errorMessage = 'has invalid value: ' . $this->value . ' for type ' . static::TYPE;
    }

    public function getValue() {
        return $this->value;
    }

    public function getMatchers() {
        return $this->matchers;
    }

    public function isValid() {
        return carr::some($this->matchers, function($matcher) {
                    preg_match($matcher, $this->value);
                });
    }

    public function getErrorMessage() {
        if ($this->isValid()) {
            return '';
        }
        $errorMessage = preg_replace("#\$value#", $this->value, $this->errorMessage);
        return $errorMessage;
    }

    public static function check($typeConfig) {
        return preg_match(static::MATCHER, $typeConfig);
    }

}
