<?php

$analyze = array(
	array(
		"name"=>"have_analyze_site",
		"label"=>"Have Site Analyze",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_analyze_item",
		"label"=>"Have Item Analyze",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_analyze_purchase",
		"label"=>"Have Purchase Analyze",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_analyze_sales",
		"label"=>"Have Sales Analyze",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_analyze_purchase_payable",
		"label"=>"Have Payable Purchase Analyze",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_analyze_sales_receivable",
		"label"=>"Have Receivable Sales Analyze",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),	
);
