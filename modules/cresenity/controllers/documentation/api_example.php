<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Api_example_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("API Example"));

        $html = View::factory('admin/page/documentation/api/example/html');
        $html = $html->render();
        $js = View::factory('admin/page/documentation/api/example/js');
        $js = $js->render();
        $app->add($html);
        $app->add_js($js);


        echo $app->render();
    }

}

// End Home Controller