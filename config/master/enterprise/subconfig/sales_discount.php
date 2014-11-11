<?php

$sales_discount = array(
	array(
		"name"=>"sales_discount",
		"label"=>"Have Sales Discount",
		"default"=>true,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_sales_discount_bill",
		"label"=>"Have Sales Discount Bill",
		"default"=>true,
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_discount"=>true,
		),
	),
	array(
		"name"=>"sales_discount_bill_print_mode",
		"label"=>"Discount Bill Print Mode",
		"default"=>false,
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"sales_discount"=>true,
			"have_sales_discount_bill"=>true,
		),
	),
	array(
		"name"=>"sales_discount_bill_print_after_checkout",
		"label"=>"Discount Bill Print After Checkout",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_discount"=>true,
			"have_sales_discount_bill"=>true,
		),
	),
	array(
		"name"=>"sales_discount_bill_org",
		"label"=>"Discount Bill Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_discount"=>true,
			"have_sales_discount_bill"=>true,
		),
	),
	
);