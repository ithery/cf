<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:02:06 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Thrown when you no longer may access the API because your quota has exceeded.
 */
final class CGeo_Exception_QuotaExceeded extends \RuntimeException implements CGeo_Exception {
    
}
