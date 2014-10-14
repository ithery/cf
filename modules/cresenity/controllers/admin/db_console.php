<?php defined('SYSPATH') OR die('No direct access allowed.');
 class Db_console_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Database Console"));

			
		$html = View::factory('admin/page/console/db/html');
		$html = $html->render();
		$js = View::factory('admin/page/console/db/js');
		$js = $js->render();
		$app->add($html);
		$app->add_js($js);
		
		
		echo $app->render();
	}

	
	
	
	
	
} // End Home Controller