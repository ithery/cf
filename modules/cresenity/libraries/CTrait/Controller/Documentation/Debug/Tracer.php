<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 28, 2018, 8:57:42 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Documentation_Debug_Tracer {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Tracer in CApp"));

        CDebug::tracer();

        echo $app->render();
    }

    public function demo() {
        $app = CApp::instance();
        $app->title(clang::__("Profiler in CApp (Demo)"));
        echo $app->render();
    }

}
