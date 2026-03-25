<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_MissingFileUploadsTraitException extends \Exception {
    use CComponent_Exception_BypassViewHandlerTrait;

    public function __construct($component) {
        parent::__construct(
            "Cannot handle file upload without [CComponent_Trait_WithFileUploadsTrait] trait on the [{$component::getName()}] component class."
        );
    }
}
