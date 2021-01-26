<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CComponent_Exception_MissingRulesException extends \Exception
{
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($component)
    {
        parent::__construct(
            "Missing [\$rules/rules()] property/method on Livewire component: [{$component}]."
        );
    }
}