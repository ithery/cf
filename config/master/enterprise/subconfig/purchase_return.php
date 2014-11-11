<?php

$purchase_return = array(
	array(
		"name"=>"purchase_return",
		"label"=>"Have Purchase Return",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),

	),
	array(
		"name"=>"have_purchase_return_bs",
		"label"=>"Have Purchase Return BS",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_return"=>true,
		),
	),
	array(
		"name"=>"have_purchase_return_bill",
		"label"=>"Have Purchase Return Bill",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_return"=>true,
		),
	),
	array(
		"name"=>"purchase_return_bill_print_mode",
		"label"=>"Purchase Return Bill Print Mode",
		"default"=>false,
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"purchase_return"=>true,
			"have_purchase_return_bill"=>true,
		),
	),
	array(
		"name"=>"purchase_return_bill_print_after_checkout",
		"label"=>"Purchase Return Bill Print After Checkout",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_return"=>true,
			"have_purchase_return_bill"=>true,
		),
	),
	array(
		"name"=>"purchase_return_bill_org",
		"label"=>"Purchase Return Bill Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_return"=>true,
			"have_purchase_return_bill"=>true,
		),
	),
	array(
		"name"=>"have_produce_debit_note_on_purchase_return",
		"label"=>"Produce Debit Note On Return",
		"default"=>'',
		"type"=>"checkbox",
		"requirement"=>array(
			"have_debit_note"=>true,
			"purchase_return"=>true,
		),
	),
	array(
		"name"=>"have_purchase_return_date",
		"label"=>"Have Purchase Return Date",
		"default"=>'',
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_return"=>true,
		),
	),
	
);