<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 1:00:04 PM
 */
trait CTrait_Controller_Documentation_Debug_Profiler {
    public function index() {
        $app = CApp::instance();
        $app->title(c::__('Profiler in CApp'));

        CDebug::bar()->enable();

        return $app;
    }

    public function demo() {
        $app = CApp::instance();
        $app->title(c::__('Profiler in CApp (Demo)'));

        return $app;
    }
}
