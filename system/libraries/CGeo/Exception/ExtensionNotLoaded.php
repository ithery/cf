<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * When a required PHP extension is missing.
 */
final class CGeo_Exception_ExtensionNotLoaded extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
}
