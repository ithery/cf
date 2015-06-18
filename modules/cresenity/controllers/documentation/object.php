<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Object extends CController {

    public function index() {
       

        $app = CApp::instance();

        $app->title(clang::__("CApp Object List"));
		
		$tabs = $app->add_tab_list();
		$tabs->add_tab('documentation-object-widget')->set_label('Widget',false)->set_ajax_url(curl::base().'documentation/object/widget');
		$tabs->add_tab('documentation-object-table')->set_label('Table',false)->set_ajax_url(curl::base().'documentation/object/table');
		$tabs->add_tab('documentation-object-tab')->set_label('Tab',false)->set_ajax_url(curl::base().'documentation/object/tab');
		$tabs->add_tab('documentation-object-nestable')->set_label('Nestable',false)->set_ajax_url(curl::base().'documentation/object/nestable');
		$tabs->add_tab('documentation-object-form')->set_label('Form',false)->set_ajax_url(curl::base().'documentation/object/form');
		$tabs->add_tab('documentation-object-action')->set_label('Action',false)->set_ajax_url(curl::base().'documentation/object/action');
		
		

        echo $app->render();
    
    }

}

// End Home Controller