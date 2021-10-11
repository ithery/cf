<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_Exception_CorruptComponentPayloadException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($component) {
        parent::__construct(
            "Component encountered corrupt data when trying to hydrate the [{$component}] component. \n"
                . "Ensure that the [name, id, data] of the Livewire component wasn't tampered with between requests."
        );
    }
}
