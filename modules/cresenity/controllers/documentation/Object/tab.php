<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Object_Tab extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Tab Example"));
        $app_list = $app->add_tab_list()->set_ajax(false);

        $data = array(
            "tab" => "Tab",
        );

        foreach ($data as $x => $n) {
            $app_tab = $app_list->add_tab();
            $app_tab->set_label($n);
            $html = CView::factory("documentation/manual/ctab/" . $x);
            $app_tab->add($html->render());
        }
        
        echo $app->render();
    }

}

// End Home Controller