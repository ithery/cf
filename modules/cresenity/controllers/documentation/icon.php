<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Icon extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Fontawesome Icon List"));
        $html = CView::factory('documentation/icon_list/fontawesome/html');
        $html = $html->render();
        $js = CView::factory('documentation/icon_list/fontawesome/js');
        $js = $js->render();
        $app->add($html);
        $app->add_js($js);


        echo $app->render();
    }

}

// End Home Controller