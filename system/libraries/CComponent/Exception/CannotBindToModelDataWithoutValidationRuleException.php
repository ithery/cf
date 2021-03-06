<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 30, 2020
 */
class CComponent_Exception_CannotBindToModelDataWithoutValidationRuleException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($key, $component) {
        parent::__construct(
            "Cannot bind property [$key] without a validation rule present in the [\$rules] array on Livewire component: [{$component}]."
        );
    }
}
