<?php

defined('SYSPATH') or die('No direct access allowed.');

use InvalidArgumentException as BaseInvalidArgumentException;
use Psr\Cache\InvalidArgumentException as InvalidArgumentExceptionContract;

class CCache_PsrBridge_Exception_InvalidArgumentException extends BaseInvalidArgumentException implements InvalidArgumentExceptionContract {
}
