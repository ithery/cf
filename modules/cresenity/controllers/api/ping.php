<?php defined('SYSPATH') OR die('No direct access allowed.');
class Ping_Controller extends CSyncController {
	
	public function index() {
		$json = csync::success_response('');
		echo $json;
		
	}
	

}