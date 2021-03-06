<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 30, 2020
 */
class CComponent_Exception_PublicPropertyTypeNotAllowedException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($componentName, $key, $value) {
        parent::__construct(
            "Livewire component's [{$componentName}] public property [{$key}] must be of type: [numeric, string, array, null, or boolean].\n"
            . "Only protected or private properties can be set as other types because JavaScript doesn't need to access them."
        );
    }
}
