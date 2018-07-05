<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 4:04:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Documentation_Forms {

    public function index() {
        $this->form();
    }

    public function form() {
        $app = CApp::instance();
        $app->title(clang::__("Forms in CApp"));
        $app->addForm();
        echo $app->render();
    }

}
