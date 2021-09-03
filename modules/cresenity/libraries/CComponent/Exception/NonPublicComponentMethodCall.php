<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_Exception_NonPublicComponentMethodCall extends \Exception {

    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($method) {
        parent::__construct('Component method not found: [' . $method . ']');
    }

}
