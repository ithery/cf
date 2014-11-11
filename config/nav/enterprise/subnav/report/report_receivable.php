<?php

return array(
	
	array(
		"name"=>"report_receivable_sales",
		"label"=>"Receivable Sales Report",
		"controller"=>"report_receivable_sales",
		"method"=>"index",
		"requirements"=>array(
			"have_sales"=>true,
			"have_sales_credit"=>true,
		),
		'action'=>cnav::report_action('receivable_sales'),
	),
	array(
		"name"=>"report_receivable_sales_payment",
		"label"=>"Receivable Sales Payment Report",
		"controller"=>"report_receivable_sales_payment",
		"method"=>"index",
		"requirements"=>array(
			"have_sales"=>true,
			"have_sales_credit"=>true,
		),
		'action'=>cnav::report_action('receivable_sales_payment'),
	),
	array(
		"name"=>"report_receivable_card",
		"label"=>"Receivable Card Report",
		"controller"=>"report_receivable_card",
		"method"=>"index",
		'action'=>cnav::report_action('receivable_card'),
		"requirements"=>array(
			"have_sales_credit"=>true,
		),
	),
	array(
		"name"=>"report_receivable_card_summary",
		"label"=>"Receivable Card Summary Report",
		"controller"=>"report_receivable_card_summary",
		"method"=>"index",
		'action'=>cnav::report_action('receivable_card_summary'),
		"requirements"=>array(
			"have_sales_credit"=>true,
		),
	),
);//end subnav report_sales_list