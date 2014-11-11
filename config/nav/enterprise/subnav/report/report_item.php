<?php

return array(
	array(
		"name"=>"report_pricelist",
		"label"=>"Pricelist Report",
		"controller"=>"report_pricelist",
		"method"=>"index",
		'action'=>cnav::report_action('pricelist'),
	),
	array(
		"name"=>"report_hpp",
		"label"=>"COGS Report",
		"controller"=>"report_hpp",
		"method"=>"index",
		'action'=>cnav::report_action('hpp'),
	),
	array(
		"name"=>"report_stock",
		"label"=>"Stock Report",
		"controller"=>"report_stock",
		"method"=>"index",
		'action'=>cnav::report_action('stock'),
	),
	array(
		"name"=>"report_stock_warehouse",
		"label"=>"Stock Warehouse Report",
		"controller"=>"report_stock_warehouse",
		"method"=>"index",
		'action'=>cnav::report_action('stock_warehouse'),
		"requirements"=>array(
			"have_warehouse"=>true,
		),
	),
	array(
		"name"=>"report_stockcard",
		"label"=>"Stock Card Report",
		"controller"=>"report_stockcard",
		"method"=>"index",
		'action'=>cnav::report_action('stockcard'),
		"requirements"=>array(
			"accounting_calculation"=>"perpetual",
		),
	),
	array(
		"name"=>"report_stockcard_period",
		"label"=>"Stock Card Report",
		"controller"=>"report_stockcard_period",
		"method"=>"index",
		'action'=>cnav::report_action('stockcard_period'),
		"requirements"=>array(
			"accounting_calculation"=>"period",
		),
	),
	array(
		"name"=>"report_stockcard_warehouse",
		"label"=>"Stockcard Warehouse Report",
		"controller"=>"report_stockcard_warehouse",
		"method"=>"index",
		'action'=>cnav::report_action('stockcard_warehouse'),
		"requirements"=>array(
			"have_warehouse"=>true,
		),
	),
	array(
		"name"=>"report_stockcard_summary",
		"label"=>"Stock Card Summary Report",
		"controller"=>"report_stockcard_summary",
		"method"=>"index",
		'action'=>cnav::report_action('stockcard_summary'),
		"requirements"=>array(
			"accounting_calculation"=>"perpetual",
		),
	),
	array(
		"name"=>"report_stockcard_period_summary",
		"label"=>"Stock Card Summary Report",
		"controller"=>"report_stockcard_period_summary",
		"method"=>"index",
		'action'=>cnav::report_action('stockcard_period_summary'),
		"requirements"=>array(
			"accounting_calculation"=>"period",
		),
	),
	array(
		"name"=>"report_stockcard_category_summary",
		"label"=>"Stock Card Category Summary Report",
		"controller"=>"report_stockcard_category_summary",
		"method"=>"index",
		'action'=>cnav::report_action('stockcard_category_summary'),
		"requirements"=>array(
			"accounting_calculation"=>"perpetual",
		),
	),
	array(
		"name"=>"report_stockcard_period_category_summary",
		"label"=>"Stock Card Category Summary Report",
		"controller"=>"report_stockcard_period_category_summary",
		"method"=>"index",
		'action'=>cnav::report_action('stockcard_period_category_summary'),
		"requirements"=>array(
			"accounting_calculation"=>"period",
		),
	),
	
	
);//end subnav report_item_list