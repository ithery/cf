<?php defined('SYSPATH') OR die('No direct access allowed.');
class Check_api_key_Controller extends CSyncController {
	
	public function index() {
		$r = CJDB::instance()->get('store',array('api_key'=>$this->api_key));
		if($r->count()<=0) {
			$json = csync::error_response('Invalid API Key');
			die($json);
		}
		
		$json = csync::success_response('');
		echo $json;
		
	}
	

}