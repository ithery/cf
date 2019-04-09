<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 4, 2019, 9:20:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
$configCollector = CConfig::instance('collector');

if ($configCollector->get('exception')) {

    // Set error handler
    set_error_handler(array('CApp', 'exceptionHandler'));

    // Set exception handler
    set_exception_handler(array('CApp', 'exceptionHandler'));
}
