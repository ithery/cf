<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 28, 2018, 8:57:42 AM
 */
trait CTrait_Controller_Documentation_Debug_Tracer {
    public function index() {
        $app = CApp::instance();
        $app->title(clang::__('Tracer in CApp'));

        CDebug::tracer();

        echo $app->render();
    }

    public function demo() {
        $app = CApp::instance();
        $app->title(clang::__('Profiler in CApp (Demo)'));
        echo $app->render();
    }
}
