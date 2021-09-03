<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:01:31 PM
 */

/**
 * Thrown when the Provider API declines the request because of wrong credentials.
 */
final class CGeo_Exception_InvalidCredentials extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
}
