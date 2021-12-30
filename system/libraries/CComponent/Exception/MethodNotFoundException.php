<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 30, 2020
 */
class CComponent_Exception_MethodNotFoundException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($method, $component) {
        parent::__construct(
            "Unable to call component method. Public method [{$method}] not found on component: [{$component}]"
        );
    }
}
