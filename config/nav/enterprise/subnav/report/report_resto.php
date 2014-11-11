<?php

return array(
	array(
		"name"=>"report_resto_menu_group",
		"label"=>"Menu Report",
		"subnav"=>array(
			array(
				"name"=>"report_resto_menu_category",
				"label"=>"Menu Category Report",
				"controller"=>"report_resto_menu_category",
				"method"=>"index",
				'action'=>cnav::report_action('resto_menu_category'),
			),
			array(
				"name"=>"report_resto_menu_subcategory",
				"label"=>"Menu Subcategory Report",
				"controller"=>"report_resto_menu_subcategory",
				"method"=>"index",
				'action'=>cnav::report_action('resto_menu_subcategory'),
			),
			array(
				"name"=>"report_resto_menu",
				"label"=>"Menu Report",
				"controller"=>"report_resto_menu",
				"method"=>"index",
				'action'=>cnav::report_action('resto_menu'),
			),
		),
	),
	array(
		"name"=>"report_resto_menu_bom",
		"label"=>"Menu BOM Report",
		"controller"=>"report_resto_menu_bom",
		"method"=>"index",
		'action'=>cnav::report_action('resto_menu_bom'),
	),
	array(
		"name"=>"report_resto_menu_group",
		"label"=>"Transaction Report",
		"subnav"=>array(
			array(
				"name"=>"report_resto_transaction",
				"label"=>"Transaction Report",
				"controller"=>"report_resto_transaction",
				"method"=>"index",
				'action'=>cnav::report_action('resto_transaction'),
			),array(
				"name"=>"report_resto_transaction_detail",
				"label"=>"Transaction Detail Report",
				"controller"=>"report_resto_transaction_detail",
				"method"=>"index",
				'action'=>cnav::report_action('resto_transaction_detail'),
			),
			array(
				"name"=>"report_resto_menu_sales",
				"label"=>"Menu Sales Report",
				"controller"=>"report_resto_menu_sales",
				"method"=>"index",
				'action'=>cnav::report_action('resto_menu_sales'),
			),
		),
	),
	array(
		"name"=>"report_resto_receipt",
		"label"=>"Receipt Report",
		"controller"=>"report_resto_receipt",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'receipt_report_cash',
				'label'=>'Cash',
			),
			array(
				'name'=>'receipt_report_item',
				'label'=>'Item',
			),
			array(
				'name'=>'receipt_report_summary',
				'label'=>'Summary',
			),
		),//end action report_resto_receipt
	),
);