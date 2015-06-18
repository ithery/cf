<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Get_started_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Get Started"));				
        $app_list = $app->add_tab_list()->set_ajax(false);

        $data = array(
            "file_structure" => "File Structure",
            "how-to-create"=>"How to Create",
        );

        foreach ($data as $x => $n) {
            $app_tab = $app_list->add_tab();
            $app_tab->set_label($n);
            $html = CView::factory("get_started/" . $x);
            $app_tab->add($html->render());
        }
        echo $app->render();
    }

}

// End Home Controller