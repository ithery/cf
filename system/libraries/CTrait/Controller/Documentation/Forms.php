<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 24, 2018, 4:04:30 PM
 */
trait CTrait_Controller_Documentation_Forms {
    public function index() {
        $this->form();
    }

    public function form() {
        $app = CApp::instance();
        $app->title(clang::__('Forms in CApp'));
        $app->addForm();
        echo $app->render();
    }
}
