<?php

defined('SYSPATH') OR die('No direct access allowed.');

class App_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("App Example"));
        $app_list = $app->add_tab_list()->set_ajax(false);

        $data = array(
            "app" => "App",
            "renderable" => "Renderable",
        );

        foreach ($data as $x => $n) {
            $app_tab = $app_list->add_tab();
            $app_tab->set_label($n);
            $html = CView::factory("documentation/manual/capp/" . $x);
            $app_tab->add($html->render());
        }
		
        echo $app->render();
    }

}

// End Home Controller