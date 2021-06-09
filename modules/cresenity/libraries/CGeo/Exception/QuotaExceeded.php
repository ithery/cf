<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:02:06 PM
 */

/**
 * Thrown when you no longer may access the API because your quota has exceeded.
 */
final class CGeo_Exception_QuotaExceeded extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
}
