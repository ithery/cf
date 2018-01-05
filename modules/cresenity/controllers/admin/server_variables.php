<?php defined('SYSPATH') OR die('No direct access allowed.');
class Server_variables_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("PHP Info"));

		$html = CView::factory('admin/page/server_variables/html');
		$html = $html->render();
		$js = CView::factory('admin/page/server_variables/js');
		$js = $js->render();
		$app->add($html);
		$app->add_js($js);
		
		echo $app->render();
	}

	
	
	
	
	
} // End Home Controller