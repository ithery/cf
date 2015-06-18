<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Clientmod extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Client Modules"));
		
		$modules = CClientModules::modules();
		
		$tabs = $app->add_tab_list();
		foreach($modules as $name=>$module) {
			$tab = $tabs->add_tab("tab_".$name);
			$tab->set_label($name);
			$tabs2 = $tab->add_tab_list();
			foreach($module as $k=>$v) {
				$tab2 = $tabs2->add_tab($name."_".$k);
				$tab2->set_label($k);
				foreach($v as $lib) {
					$tab2->add("<p>".$lib."</p>");
				}
				
			}
		}
		
        echo $app->render();
    }

}

// End Home Controller