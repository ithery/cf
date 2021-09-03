<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 30, 2020 
 * @license Ittron Global Teknologi
 */


class CComponent_Exception_PublicPropertyNotFoundException extends \Exception
{
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($property, $component)
    {
        parent::__construct(
            "Unable to set component data. Public property [\${$property}] not found on component: [{$component}]"
        );
    }
}
