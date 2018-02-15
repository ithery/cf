<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation extends CController {

    public function index() {

        $app = CApp::instance();

        $app->title(clang::__("CAPP Documentation"));

        $tabs = $app->add_tab_list();
        $tabs->add_tab('documentation-object')->set_label('Object')->set_ajax_url(curl::base() . 'documentation/object');
        $tabs->add_tab('documentation-database')->set_label('Database')->set_ajax_url(curl::base() . 'documentation/database');
        $tabs->add_tab('documentation-icon')->set_label('Icon')->set_ajax_url(curl::base() . 'documentation/icon');
        $tabs->add_tab('documentation-ftp')->set_label('FTP')->set_ajax_url(curl::base() . 'documentation/ftp');



        echo $app->render();
    }

}

// End Home Controller