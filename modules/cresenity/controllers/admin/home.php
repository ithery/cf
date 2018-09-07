<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Admin_Home extends CController {

    public function index() {

        $app = CApp::instance();

        $app->title(clang::__("Dashboard"));


        $html = CView::factory('admin/page/dashboard/html');

        $html = $html->render();

        $js = CView::factory('admin/page/dashboard/js');
        $js = $js->render();

        $app->add($html);
        $app->add_js($js);


        echo $app->render();
    }

}

// End Home Controller