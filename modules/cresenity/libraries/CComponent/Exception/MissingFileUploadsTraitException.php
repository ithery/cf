<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 30, 2020 
 * @license Ittron Global Teknologi
 */


class CComponent_Exception_MissingFileUploadsTraitException extends \Exception
{
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($component)
    {
        parent::__construct(
            "Cannot handle file upload without [CComponent_Trait_WithFileUploadsTrait] trait on the [{$component::getName()}] component class."
        );
    }
}
