<?php
 
return	array(
		array(
			"name"=>"resto_menu_menu_list",
			"label"=>"Menu",
			"subnav"=>array(
				array(
					"name"=>"resto_menu_category",
					"label"=>"Menu Category",
					"controller"=>"resto_menu_category",
					"method"=>"index",
				),
				array(
					"name"=>"resto_menu_subcategory",
					"label"=>"Menu Subcategory",
					"controller"=>"resto_menu_subcategory",
					"method"=>"index",
				),
				array(
					"name"=>"resto_menu",
					"label"=>"Menu",
					"controller"=>"resto_menu",
					"method"=>"index",
					'action'=>array(
						array(
							'name'=>'edit_resto_menu',
							'label'=>'Edit',
							'controller'=>'resto_menu',
							'method'=>'edit',
						),
						
					),
				),
			),
		),
		array(
			"name"=>"resto_member_menu_list",
			"label"=>"Member",
			"subnav"=>array(
				array(
					"name"=>"resto_member_type",
					"label"=>"Member Type",
					"controller"=>"resto_member_type",
					"method"=>"index",
				),
				array(
					"name"=>"resto_member",
					"label"=>"Member",
					"controller"=>"resto_member",
					"method"=>"index",
				),
				
			),
		),
		array(
			"name"=>"resto_kitchen",
			"label"=>"Kitchen",
			"controller"=>"resto_kitchen",
			"method"=>"index",
		),
		array(
			"name"=>"resto_table_management_menu",
			"label"=>"Manage Table",
			"subnav"=>array(
				array(
					"name"=>"resto_floor",
					"label"=>"Floor",
					"controller"=>"resto_floor",
					"method"=>"index",
				),
				array(
					"name"=>"resto_table",
					"label"=>"Table",
					"controller"=>"resto_table",
					"method"=>"index",
				),
			),
		),
		array(
			"name"=>"resto_payment_type_menu",
			"label"=>"Payment Type",
			"subnav"=>array(
				array(
					"name"=>"resto_payment_type_group",
					"label"=>"Payment Type Group",
					"controller"=>"resto_payment_type_group",
					"method"=>"index",
				),
				array(
					"name"=>"resto_payment_type",
					"label"=>"Payment Type",
					"controller"=>"resto_payment_type",
					"method"=>"index",
				),
			),
		),
		array(
			"name"=>"resto_promo",
			"label"=>"Promo",
			"controller"=>"resto_promo",
			"method"=>"index",
		),
		array(
			"name"=>"resto_menu_sales_menu",
			"label"=>"Menu Sales",
			"subnav"=>array(
				array(
					"name"=>"resto_approve_menu_sales",
					"label"=>"Approve Menu Sales",
					"controller"=>"resto_approve_menu_sales",
					"method"=>"index",
					'action'=>array(
						array(
							'name'=>'approve_menu_sales',
							'label'=>'Approve',
							'controller'=>'resto_approve_menu_sales',
							'method'=>'approve',
						),
						array(
							'name'=>'delete_menu_sales',
							'label'=>'Delete',
							'controller'=>'resto_approve_menu_sales',
							'method'=>'delete',
						),
					),
				),
				array(
					"name"=>"resto_menu_sales_list",
					"label"=>"Menu Sales List",
					"controller"=>"resto_menu_sales",
					"method"=>"index",
					'action'=>array(
						array(
							'name'=>'detail_menu_sales',
							'label'=>'Detail',
							'controller'=>'resto_menu_sales',
							'method'=>'detail',
						),
						array(
							'name'=>'delete_menu_sales',
							'label'=>'Delete',
							'controller'=>'resto_menu_sales',
							'method'=>'delete',
						),
					),
				),
			),
		),
	);
	