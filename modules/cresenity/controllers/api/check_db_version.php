<?php defined('SYSPATH') OR die('No direct access allowed.');
class Check_db_version_Controller extends CSyncController {
	
	public function index() {
		$r = CJDB::instance()->get('store',array('api_key'=>$this->api_key));
		if($r->count()<=0) {
			$json = csync::error_response('Invalid API Key');
			die($json);
		}
		$version_data = cdata::get('current_client_version');
		$db_version = '';
		if(is_array($version_data)&&isset($version_data['db_version'])) {
			$db_version = $version_data["db_version"];
		}
		$data=array();
		$data['db_version']=$db_version;
		$json = csync::success_response($data);
		echo $json;
		
	}
	

}