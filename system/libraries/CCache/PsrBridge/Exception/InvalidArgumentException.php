<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 1:03:26 PM
 */
use InvalidArgumentException as BaseInvalidArgumentException;
use Psr\Cache\InvalidArgumentException as InvalidArgumentExceptionContract;

class CCache_PsrBridge_Exception_InvalidArgumentException extends BaseInvalidArgumentException implements InvalidArgumentExceptionContract {
}
