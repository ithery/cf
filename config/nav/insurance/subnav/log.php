<?php
return array(
	array(
		"name"=>"log_login_menu",
		"label"=>"Login",
		"subnav"=>array(
			array(
				"name"=>"log_login",
				"label"=>"Login Success Log",
				"controller"=>"log_login",
				"method"=>"index",
				
				'action'=>array(
					array(
						'name'=>'download_xls_log_login',
						'label'=>'Download XLS',
					),
				),
			),
			array(
				"name"=>"log_login_fail",
				"label"=>"Login Fail Log",
				"controller"=>"log_login_fail",
				"method"=>"index",
				"requirements"=>array(
					"have_log_login_fail"=>true,
				),
				'action'=>array(
					array(
						'name'=>'download_xls_log_login_fail',
						'label'=>'Download XLS',
					),
				),
			),
			
		),
	),
	array(
		"name"=>"log_request",
		"label"=>"Request Log",
		"controller"=>"log_request",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'download_xls_log_request',
				'label'=>'Download XLS',
			),
		),
		
	),
	
	array(
		"name"=>"log_activity",
		"label"=>"Activity Log",
		"controller"=>"log_activity",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'download_xls_log_activity',
				'label'=>'Download XLS',
			),
		),
		
	),
	
);