<?php

/**
 * Description of home
 *
 * @author Hery
 */
Class Controller_Home extends CController {

    public function index() {
        $app = CApp::instance();

        return $app;
    }

}
