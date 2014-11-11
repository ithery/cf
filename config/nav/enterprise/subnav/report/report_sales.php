<?php

return array(
	array(
		"name"=>"report_sales",
		"label"=>"Sales Report",
		"controller"=>"report_sales",
		"method"=>"index",
		'action'=>cnav::report_action('sales'),
		
	),
	array(
		"name"=>"report_sales_commission",
		"label"=>"Sales Commission Report",
		"controller"=>"report_sales_commission",
		"method"=>"index",
		'action'=>cnav::report_action('sales_commission'),
		"requirements"=>array(
			"have_salesman_commission"=>true,
		),
	),
	array(
		"name"=>"report_sales_detail",
		"label"=>"Sales Detail Report",
		"controller"=>"report_sales_detail",
		"method"=>"index",
		'action'=>cnav::report_action('sales_detail'),
		"requirements"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"report_sales_return",
		"label"=>"Sales Return Report",
		"controller"=>"report_sales_return",
		"method"=>"index",
		'action'=>cnav::report_action('sales_return'),
		
	),
	array(
		"name"=>"report_sales_result_detail_period",
		"label"=>"Sales Result Detail Report",
		"controller"=>"report_sales_result_detail_period",
		"method"=>"index",
		"requirements"=>array(
			"have_sales"=>true,
			"have_sales_credit"=>true,
		),
		'action'=>cnav::report_action('sales_result_detail_period'),
		"requirements"=>array(
			"accounting_calculation"=>"period",
		),
	),
);//end subnav report_sales_list