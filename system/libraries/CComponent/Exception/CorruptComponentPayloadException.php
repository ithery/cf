<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_CorruptComponentPayloadException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($component) {
        parent::__construct(
            "Component encountered corrupt data when trying to hydrate the [{$component}] component. \n"
                . "Ensure that the [name, id, data] of the Livewire component wasn't tampered with between requests."
        );
    }
}
