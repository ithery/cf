<?php defined('SYSPATH') OR die('No direct access allowed.');
 class Shell_console_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Shell Console"));

			
		$html = View::factory('admin/page/console/shell/html');
		$html = $html->render();
		$js = View::factory('admin/page/console/shell/js');
		$js = $js->render();
		$app->add($html);
		$app->add_js($js);
		
		
		echo $app->render();
	}

	
	
	
	
	
} // End Home Controller