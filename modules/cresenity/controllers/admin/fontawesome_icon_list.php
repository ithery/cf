<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Fontawesome_icon_list_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Fontawesome Icon List"));


        $html = View::factory('admin/page/documentation/icon_list/fontawesome/html');
        $html = $html->render();
        $js = View::factory('admin/page/documentation/icon_list/fontawesome/js');
        $js = $js->render();
        $app->add($html);
        $app->add_js($js);


        echo $app->render();
    }

}

// End Home Controller