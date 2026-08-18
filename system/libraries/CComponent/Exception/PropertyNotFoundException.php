<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_PropertyNotFoundException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($property, $component) {
        parent::__construct(
            "Property [\${$property}] not found on component: [{$component}]"
        );
    }
}
