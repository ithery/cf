<?php

return array(
	array(
		"name"=>"report_purchase_order",
		"label"=>"Purchase Order Report",
		"controller"=>"report_purchase_order",
		"method"=>"index",
		'action'=>cnav::report_action('purchase_order'),
		
		"requirements"=>array(
			"purchase_order"=>true,
		),
		//end action report_purchase_order
	),
	array(
		"name"=>"report_purchase_order_detail",
		"label"=>"Purchase Order Detail Report",
		"controller"=>"report_purchase_order_detail",
		"method"=>"index",
		'action'=>cnav::report_action('purchase_order_detail'),
		"requirements"=>array(
			"purchase_order"=>true,
		),
		//end action report_purchase_order_detail
	),
	array(
		"name"=>"report_purchase",
		"label"=>"Purchase Report",
		"controller"=>"report_purchase",
		"method"=>"index",
		'action'=>cnav::report_action('purchase'),
		"requirements"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"report_purchase_detail",
		"label"=>"Purchase Detail Report",
		"controller"=>"report_purchase_detail",
		"method"=>"index",
		'action'=>cnav::report_action('purchase_detail'),
		"requirements"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"report_purchase_return",
		"label"=>"Purchase Return Report",
		"controller"=>"report_purchase_return",
		"method"=>"index",
		'action'=>cnav::report_action('purchase_return'),
		"requirements"=>array(
			"have_purchase_return"=>true,
		),
	),
);//end subnav report_purchase_list