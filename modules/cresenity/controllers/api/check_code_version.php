<?php defined('SYSPATH') OR die('No direct access allowed.');
class Check_code_version_Controller extends CSyncController {
	
	public function index() {
		$r = CJDB::instance()->get('store',array('api_key'=>$this->api_key));
		if($r->count()<=0) {
			$json = csync::error_response('Invalid API Key');
			die($json);
		}
		$version_data = cdata::get('current_client_version');
		$code_version = '';
		$download_link = '';
		if(is_array($version_data)&&isset($version_data['code_version'])) {
			$code_version = $version_data["code_version"];
		}
		if(is_array($version_data)&&isset($version_data['download_link'])) {
			$download_link = $version_data["download_link"];
		}
		$data=array();
		$data['code_version']=$code_version;
		$data['download_link']=$download_link;
		$json = csync::success_response($data);
		echo $json;
		
	}
	

}