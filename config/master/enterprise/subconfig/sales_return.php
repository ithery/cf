<?php

$sales_return = array(
	array(
		"name"=>"sales_return",
		"label"=>"Have Sales Return",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
			
	),
	array(
		"name"=>"have_sales_return_bs",
		"label"=>"Have Sales Return BS",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_return"=>true,
		),
			
	),
	array(
		"name"=>"have_sales_return_bill",
		"label"=>"Have Sales Return Bill",
		"default"=>true,
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_return"=>true,
		),
	),
	array(
		"name"=>"sales_return_bill_print_mode",
		"label"=>"Sales Return Bill Print Mode",
		"default"=>false,
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"sales_return"=>true,
			"have_sales_return_bill"=>true,
		),
	),
	array(
		"name"=>"sales_return_bill_print_after_checkout",
		"label"=>"Sales Return Bill Print After Checkout",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_return"=>true,
			"have_sales_return_bill"=>true,
		),
	),
	array(
		"name"=>"sales_return_bill_org",
		"label"=>"Sales Return Bill Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_return"=>true,
			"have_sales_return_bill"=>true,
		),
	),
	array(
		"name"=>"have_produce_credit_note_on_sales_return",
		"label"=>"Produce Credit Note On Return",
		"default"=>'',
		"type"=>"checkbox",
		"requirement"=>array(
			"have_credit_note"=>true,
			"sales_return"=>true,
		),
	),
	array(
		"name"=>"have_sales_return_date",
		"label"=>"Have Sales Return Date",
		"default"=>'',
		"type"=>"checkbox",
		"requirement"=>array(
			"sales_return"=>true,
		),
	),
);