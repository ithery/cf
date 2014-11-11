<?php



$config["cnav"]=array(
	array(
		"name"=>"dashboard",
		"label"=>"Dashboard",
		"controller"=>"home",
		"method"=>"index",
		"icon"=>"home",
	),
	
	array(
		"name"=>"master_data",
		"label"=>"Master Data",
		"icon"=>"gift",
		"subnav"=>array(
			array(
				"name"=>"customer",
				"label"=>"Customer",
				"controller"=>"customer",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'add_customer',
						'label'=>'Add',
						'controller'=>'customer',
						'method'=>'add',
					),
					array(
						'name'=>'edit_customer',
						'label'=>'Edit',
						'controller'=>'customer',
						'method'=>'edit',
					),
					array(
						'name'=>'delete_customer',
						'label'=>'Delete',
						'controller'=>'customer',
						'method'=>'delete',
					),
					
				),
			),
			array(
				"name"=>"driver",
				"label"=>"Driver",
				"controller"=>"driver",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'add_driver',
						'label'=>'Add',
						'controller'=>'driver',
						'method'=>'add',
					),
					array(
						'name'=>'edit_driver',
						'label'=>'Edit',
						'controller'=>'driver',
						'method'=>'edit',
					),
					array(
						'name'=>'delete_driver',
						'label'=>'Delete',
						'controller'=>'driver',
						'method'=>'delete',
					),
					
				),
			),
			array(
				"name"=>"car_type",
				"label"=>"Car Type",
				"controller"=>"car_type",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'add_car_type',
						'label'=>'Add',
						'controller'=>'car_type',
						'method'=>'add',
					),
					array(
						'name'=>'edit_car_type',
						'label'=>'Edit',
						'controller'=>'car_type',
						'method'=>'edit',
					),
					array(
						'name'=>'delete_car_type',
						'label'=>'Delete',
						'controller'=>'car_type',
						'method'=>'delete',
					),
					
				),
			),
			array(
				"name"=>"car",
				"label"=>"Car",
				"controller"=>"car",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'add_car',
						'label'=>'Add',
						'controller'=>'car',
						'method'=>'add',
					),
					array(
						'name'=>'edit_car',
						'label'=>'Edit',
						'controller'=>'car',
						'method'=>'edit',
					),
					array(
						'name'=>'delete_car',
						'label'=>'Delete',
						'controller'=>'car',
						'method'=>'delete',
					),
					
				),
			),
			array(
				"name"=>"city",
				"label"=>"City",
				"controller"=>"city",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'add_city',
						'label'=>'Add',
						'controller'=>'city',
						'method'=>'add',
					),
					array(
						'name'=>'edit_city',
						'label'=>'Edit',
						'controller'=>'city',
						'method'=>'edit',
					),
					array(
						'name'=>'delete_city',
						'label'=>'Delete',
						'controller'=>'city',
						'method'=>'delete',
					),
					
				),
			),
		),
	),
	array(
		"name"=>"routes_data",
		"label"=>"Routes",
		"icon"=>"repeat",
		"subnav"=>array(
			array(
				"name"=>"route",
				"label"=>"Route",
				"controller"=>"route",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'add_route',
						'label'=>'Add',
						'controller'=>'route',
						'method'=>'add',
					),
					array(
						'name'=>'edit_route',
						'label'=>'Edit',
						'controller'=>'route',
						'method'=>'edit',
					),
					array(
						'name'=>'delete_route',
						'label'=>'Delete',
						'controller'=>'route',
						'method'=>'delete',
					),
					
				),
			),
			array(
				"name"=>"passenger",
				"label"=>"Passenger List",
				"controller"=>"passenger",
				"method"=>"index",
				'action'=>array(
					
					array(
						'name'=>'delete_passenger',
						'label'=>'Delete',
						'controller'=>'passenger',
						'method'=>'delete',
					),
					
				),
			),
			
		),
	),
	array(
		"name"=>"report_data",
		"label"=>"Report",
		"icon"=>"file",
		"subnav"=>array(
			array(
				"name"=>"report_route",
				"label"=>"Route",
				"controller"=>"report_route",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'download_xls',
						'label'=>'Download XLS',
					),
					
					
				),
			),
			array(
				"name"=>"report_passenger",
				"label"=>"Passenger",
				"controller"=>"report_passenger",
				"method"=>"index",
				'action'=>array(
					array(
						'name'=>'download_xls',
						'label'=>'Download XLS',
					),
					
					
				),
			),
			
		),
	),
	array(
		"name"=>"report_analyze",
		"label"=>"Analyze",
		"icon"=>"bar-chart",
		"subnav"=>array(
			array(
				"name"=>"analyze_site",
				"label"=>"Site Analyze",
				"controller"=>"analyze_site",
				"method"=>"index",
				
			),
			
		),
	),
	array(
		"name"=>"log_list",
		"label"=>"Log",
		"icon"=>"file-text",
		"subnav"=>array(
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
			
		),
	),//end log_list
	
	array(
		"name"=>"setting_list",
		"label"=>"Setting",
		"icon"=>"cog",
		"subnav"=>array(
			array(
				"name"=>"access",
				"label"=>"Access",
				"subnav"=>array(
					array(
						"name"=>"roles",
						"label"=>"Roles",
						"controller"=>"roles",
						"method"=>"index",
						'action'=>array(
							array(
								'name'=>'add_roles',
								'label'=>'Add',
								'controller'=>'roles',
								'method'=>'add',
							),
							array(
								'name'=>'edit_roles',
								'label'=>'Edit',
								'controller'=>'roles',
								'method'=>'edit',
							),
							array(
								'name'=>'delete_roles',
								'label'=>'Delete',
								'controller'=>'roles',
								'method'=>'delete',
							),
						),//end action roles
					),
					array(
						"name"=>"users",
						"label"=>"Users",
						"controller"=>"users",
						"method"=>"index",
						'action'=>array(
							array(
								'name'=>'add_users',
								'label'=>'Add',
								'controller'=>'users',
								'method'=>'add',
							),
							array(
								'name'=>'edit_users',
								'label'=>'Edit',
								'controller'=>'users',
								'method'=>'edit',
							),
							array(
								'name'=>'delete_users',
								'label'=>'Delete',
								'controller'=>'users',
								'method'=>'delete',
							),
						),//end action users
					),
					array(
						"name"=>"user_permission",
						"label"=>"Users Permission",
						"controller"=>"user_permission",
						"method"=>"index",	
					),
				),//end subnav access
			),
			
			
	
		),
	),//end setting_list
);
