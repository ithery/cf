<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Database_Controller extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Database Example"));
        $db_list = $app->add_tab_list()->set_ajax(false);

        $data = array(
            "db"=> "Database",
//            "escape"=>"Escape",
//            "escape_str"=>"Escape String",
////            "escape_column"=>"Escape Column",
//            "insert" => "Insert",
//            "update"=>"Update",
//            "delete"=>"Delete",
        );

        foreach ($data as $d => $b) {
            $db_tab = $db_list->add_tab();
            $db_tab->set_label($b);
            $html = CView::factory("documentation/manual/cdatabase/" . $d);
            $db_tab->add($html->render());
        }
        echo $app->render();
    }

}

// End Home Controller