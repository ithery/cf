<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 *
 * @since Apr 4, 2019, 9:20:01 PM
 *
 * @license Ittron Global Teknologi <ittron.co.id>
 */
CBootstrap::instance()->addBootstrapper([
    CApp_Bootstrapper_DependencyChecker::class,
]);

$domain = CF::domain();

if (CF::config('collector.exception')) {
    CException::exceptionHandler()->reportable(function (Exception $e) {
        CCollector::exception($e);
    });
}

if (CF::config('app.mail_error')) {
    CException::exceptionHandler()->reportable(function (Exception $e) {
        CApp::sendExceptionEmail($e);
    });
}

if (carr::first(explode('/', trim(CFRouter::getUri(), '/'))) == 'administrator') {
    //we adjust the the client modules
    CManager::registerModule('jquery.datatable', [
        'css' => ['administrator/datatables/datatables.css'],
        'js' => ['administrator/datatables/datatables.js'],
    ]);
}

CApp::registerBlade();
CApp::registerComponent();
CApp::registerControl();
if (isset($_COOKIE['capp-profiler'])) {
    CProfiler::enable();
}
if (isset($_COOKIE['capp-debugbar'])) {
    CDebug::bar()->enable();
}
