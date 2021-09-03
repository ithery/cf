<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:58:22 PM
 */

/**
 * When a required PHP extension is missing.
 */
final class CGeo_Exception_ExtensionNotLoaded extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
}
