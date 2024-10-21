<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Thrown when you no longer may access the API because your quota has exceeded.
 */
final class CGeo_Exception_QuotaExceeded extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
}
