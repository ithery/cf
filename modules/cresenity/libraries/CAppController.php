<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Cresenity PHP Library.
 * @author     Hery Kurniawan
 */
class CAppController extends CController {

    public function __construct() {
        parent::__construct();
        $app = CApp::instance();
        $controller = crouter::controller();
        $method = crouter::method();

        if (!cnav::have_access()) {
            $nav = cnav::nav();
            $app->title($nav['label']);
            $app->add_div()->add_class("well")->add("Sorry, you don't have permission");
            die($app->render());
        }
    }

}
