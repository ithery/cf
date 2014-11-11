<?php

$purchase_discount = array(
	array(
		"name"=>"have_purchase_discount",
		"label"=>"Have Purchase Discount",
		"default"=>true,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_purchase_detail_discount",
		"label"=>"Have Purchase Detail Discount",
		"default"=>true,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_purchase_discount_bill",
		"label"=>"Have Purchase Discount Bill",
		"default"=>true,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_discount"=>true,
		),
	),
	array(
		"name"=>"purchase_discount_bill_print_mode",
		"label"=>"Discount Bill Print Mode",
		"default"=>false,
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"purchase_discount"=>true,
			"have_purchase_discount_bill"=>true,
		),
	),
	array(
		"name"=>"purchase_discount_bill_print_after_checkout",
		"label"=>"Discount Bill Print After Checkout",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_discount"=>true,
			"have_purchase_discount_bill"=>true,
		),
	),
	array(
		"name"=>"purchase_discount_bill_org",
		"label"=>"Discount Bill Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_discount"=>true,
			"have_purchase_discount_bill"=>true,
		),
	),
	
);