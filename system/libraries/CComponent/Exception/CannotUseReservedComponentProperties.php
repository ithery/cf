<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_Exception_CannotUseReservedComponentProperties extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($propertyName, $componentName) {
        parent::__construct(
            "Public property [{$propertyName}] on [{$componentName}] component is reserved for internal Livewire use."
        );
    }
}
