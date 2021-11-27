<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 30, 2020
 */
CPagination_Paginator::useBootstrap();

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 4, 2019, 9:20:01 PM
 */
CBootstrap::instance()->addBootstrapper([
    CApp_Bootstrapper_DependencyChecker::class,
]);

$domain = CF::domain();

CException::init();

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

CFBenchmark::start('CApp_Bootstrap');
CApp::registerBlade();
CApp::registerComponent();

CApp::registerControl();
CFBenchmark::stop('CApp_Bootstrap');

if (isset($_COOKIE['capp-profiler'])) {
    CProfiler::enable();
}
if (isset($_COOKIE['capp-debugbar'])) {
    CDebug::bar()->enable();
}

CApp_Auth_Features::setFeatures(CF::config('app.auth.features'));

if (CF::isTesting()) {
    CEvent::dispatcher()->listen(CLogger_Event_MessageLogged::class, function (CLogger_Event_MessageLogged $event) {
        if (isset($event->context['exception'])) {
            CTesting::loggedExceptionCollection()->push($event->context['exception']);
        }
    });
}
