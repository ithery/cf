<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_NonPublicComponentMethodCall extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($method) {
        parent::__construct('Component method not found: [' . $method . ']');
    }
}
