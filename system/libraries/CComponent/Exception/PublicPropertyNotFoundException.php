<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_PublicPropertyNotFoundException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($property, $component) {
        parent::__construct(
            "Unable to set component data. Public property [\${$property}] not found on component: [{$component}]"
        );
    }
}
