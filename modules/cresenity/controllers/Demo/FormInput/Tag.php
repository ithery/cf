<?php
	class Controller_Demo_FormInput_Tag extends CController {
		
		public function index() {
			$app = CApp::instance();
			
			$form = $app->add_form();
			$tag_control= $form->add_control('tag_input','tag');
			
			echo $app->render();
		}
		
	}
?>