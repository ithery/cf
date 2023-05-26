<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Thrown when the Provider API declines the request because of wrong credentials.
 */
final class CGeo_Exception_InvalidCredentials extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
}
