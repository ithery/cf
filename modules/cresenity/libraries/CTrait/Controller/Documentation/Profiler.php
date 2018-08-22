<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 1:00:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Documentation_Profiler {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Profiler in CApp"));

        CDebug::bar()->enable();

        echo $app->render();
    }

    public function demo() {
        $app = CApp::instance();
        $app->title(clang::__("Profiler in CApp (Demo)"));
        echo $app->render();
    }

}
