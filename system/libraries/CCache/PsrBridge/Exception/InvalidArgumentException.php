<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:03:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use InvalidArgumentException as BaseInvalidArgumentException;
use Psr\Cache\InvalidArgumentException as InvalidArgumentExceptionContract;

class CCache_PsrBridge_Exception_InvalidArgumentException extends BaseInvalidArgumentException implements InvalidArgumentExceptionContract {
    
}
