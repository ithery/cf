<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Object_Table extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Table Example"));
        $table_list = $app->add_tab_list()->set_ajax(false);

        $data = array(
            "table"=>"Table",
//            "cell_callback_func"=>"Cell CallBack Func",
//            "set_checkbox"=>"Checkbox",
//            "set_data_from_query"=>"Set Data Dari Query",
        );
        
        foreach($data as $k=>$v) {
            $table_tab = $table_list->add_tab();
            $table_tab->set_label($v);
            $html = CView::factory("documentation/manual/ctable/".$k);
            $table_tab->add($html->render());            
        }

        echo $app->render();
    }
}

// End Home Controller