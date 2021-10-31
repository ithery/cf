<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_Exception_RootTagMissingFromViewException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct() {
        parent::__construct(
            'Component rendering encountered a missing root tag when trying to render a '
                . "component. \n When rendering a Blade view, make sure it contains a root HTML tag."
        );
    }
}
