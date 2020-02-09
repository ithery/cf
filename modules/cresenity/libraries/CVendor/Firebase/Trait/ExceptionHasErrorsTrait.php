<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @codeCoverageIgnore
 */
trait CVendor_Firebase_Trait_ExceptionHasErrorsTrait {

    /** @var string[] */
    protected $errors = [];

    /**
     * @return string[]
     */
    public function errors() {
        return $this->errors;
    }

}
