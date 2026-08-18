<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_MethodNotFoundException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($method, $component) {
        parent::__construct(
            "Unable to call component method. Public method [{$method}] not found on component: [{$component}]"
        );
    }
}
