<?php defined('SYSPATH') OR die('No direct access allowed.');
class Table_example_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Table Example"));

			
		$html = View::factory('admin/page/documentation/table/example/html');
		$html = $html->render();
		$js = View::factory('admin/page/documentation/table/example/js');
		$js = $js->render();
		$app->add($html);
		$app->add_js($js);
		
		
		echo $app->render();
	}

} // End Home Controller