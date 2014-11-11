<?php

return array(
	
	array(
		"name"=>"report_payable_purchase",
		"label"=>"Payable Purchase Report",
		"controller"=>"report_payable_purchase",
		"method"=>"index",
		"requirements"=>array(
			"have_purchase_credit"=>true,
		),
		'action'=>cnav::report_action('payable_purchase'),
		"requirements"=>array(
			"have_purchase_credit"=>true,
		),
	),
	
	array(
		"name"=>"report_payable_purchase_payment",
		"label"=>"Payable Purchase Payment Report",
		"controller"=>"report_payable_purchase_payment",
		"method"=>"index",
		"requirements"=>array(
			"have_purchase_credit"=>true,
		),
		'action'=>cnav::report_action('payable_purchase_payment'),
		"requirements"=>array(
			"have_purchase_credit"=>true,
		),
	),
	array(
		"name"=>"report_payable_card",
		"label"=>"Payable Card Report",
		"controller"=>"report_payable_card",
		"method"=>"index",
		'action'=>cnav::report_action('payable_card'),
		"requirements"=>array(
			"have_purchase_credit"=>true,
		),
	),
	array(
		"name"=>"report_payable_card_summary",
		"label"=>"Payable Card Summary Report",
		"controller"=>"report_payable_card_summary",
		"method"=>"index",
		'action'=>cnav::report_action('payable_card_summary'),
		"requirements"=>array(
			"have_purchase_credit"=>true,
		),
	),
);//end subnav report_purchase_list