<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_CannotUseReservedComponentProperties extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($propertyName, $componentName) {
        parent::__construct(
            "Public property [{$propertyName}] on [{$componentName}] component is reserved for internal Livewire use."
        );
    }
}
