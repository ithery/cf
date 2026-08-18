<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Exception_ComponentNotFoundException extends \Exception implements \CDebug_Contract_ShouldNotCollectException {
    use CComponent_Exception_BypassViewHandlerTrait;
}
