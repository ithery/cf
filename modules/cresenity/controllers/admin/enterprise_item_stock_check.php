<?php defined('SYSPATH') OR die('No direct access allowed.');
	
class Enterprise_item_stock_check_Controller extends CController {
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Item Stock Check"));
		
		
		
		
		$db = CDatabase::instance();
		
		
		$cdb = CJDB::instance();
		$org_list=$cdb->get_list('org','org_id','name');
		foreach($org_list as $k=>$v) {
			$org_id = $k;
			break;
		}
		$get = $_GET;
		if(isset($get["org_id"])) {
			$org_id = $get["org_id"];
		}
		$widget = $app->add_widget()->set_icon('filter')->set_title('Filter');
		$form = $widget->add_form()->set_method('get');
		$form->add_field()->set_label(clang::__('Organization'))->add_control('org_id','select')->set_value($org_id)->set_list($org_list)->set_submit_onchange(true);
		
		

		
		
		$html = CView::factory('admin/page/enterprise/item_stock_check/html');
		$html->org_id = $org_id;
		$html = $html->render();
		
		$js = CView::factory('admin/page/enterprise/item_stock_check/js');
		$js = $js->render();
		$app->add($html);
		$app->add_js($js);
		
		echo $app->render();
	}

	
	
	
	
	
} // End Home Controller