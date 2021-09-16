<?php

class CComponent_Exception_MissingRulesException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($component) {
        parent::__construct(
            "Missing [\$rules/rules()] property/method on Livewire component: [{$component}]."
        );
    }
}
