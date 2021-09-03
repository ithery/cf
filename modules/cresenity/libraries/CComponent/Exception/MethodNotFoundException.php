<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 30, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_Exception_MethodNotFoundException extends \Exception {

    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($method, $component) {
        parent::__construct(
                "Unable to call component method. Public method [{$method}] not found on component: [{$component}]"
        );
    }

}
