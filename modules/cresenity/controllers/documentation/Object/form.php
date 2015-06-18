<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Object_Form extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Form Example"));
        $table_list = $app->add_tab_list()->set_ajax(false);

        $data = array(
            "form" => "Form",
            "form-field" => "Form Field",
            "input-text" => "Input Text",
            "currency" => "Input Currency",
            "upload" => "Upload",
            "download" => "Download",
            "ckeditor" => "CKEditor",
        );
        foreach ($data as $k => $v) {
            $table_tab = $table_list->add_tab();
            $table_tab->set_label($v);
            $html = CView::factory("documentation/manual/cform/" . $k);
            $table_tab->add($html->render());
        }
        echo $app->render();
    }

    public function tab($method) {
        $this->$method();
    }

    public function intro() {
        $div = CDivElement::factory();
        echo $div->json();
    }

}

// End Home Controller