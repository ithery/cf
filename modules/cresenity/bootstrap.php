<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 4, 2019, 9:20:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
$domain = CF::domain();
$isAppBox = strpos($domain, 'app.ittron.co.id') !== false || strpos($domain, 'cpanel.ittron.co.id') !== false;
if ($isAppBox) {
    //$whoops = new \Whoops\Run;
    //$whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
    //$whoops->register();
} else {
// Set error handler
    set_error_handler(array('CApp', 'exceptionHandler'));

// Set exception handler
    set_exception_handler(array('CApp', 'exceptionHandler'));
}


if (carr::first(explode("/", trim(CFRouter::getUri(), "/"))) == "administrator") {

    //we adjust the the client modules
    CManager::registerModule('jquery.datatable', array(
        "css" => array("administrator/datatables/datatables.css"),
        "js" => array("administrator/datatables/datatables.js"),
    ));
}
CFConsole::addCommand([
    CQC_Console_Command_PhpUnitCommand::class,
    CQC_Console_Command_PhpUnitListCommand::class,
]);


