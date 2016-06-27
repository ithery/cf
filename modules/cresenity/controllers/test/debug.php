<?php

class Controller_Test_Debug extends CController {
	
	
	public function index() {
		$app = CApp::instance();
		
		$app->set_login_required(false);
		
		echo $app->render();
	}
	
}