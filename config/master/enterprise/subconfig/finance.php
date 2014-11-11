<?php

$finance = array(
	
	array(
		"name"=>"have_finance",
		"label"=>"Have Finance",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_accounting"=>true,
		),
	),
	array(
		"name"=>"have_bank_in_out",
		"label"=>"Have Bank In/Out",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_finance"=>true,
		),
	),
	array(
		"name"=>"have_giro_is_cash",
		"label"=>"Have Giro is Cash",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_finance"=>true,
		),
	),
	array(
		"name"=>"cash_out_code_format",
		"label"=>"Cash Out Code Format",
		"default"=>'',
		"type"=>"text",
		"requirement"=>array(
			"have_finance"=>true,
		),
	),
	array(
		"name"=>"cash_in_code_format",
		"label"=>"Cash In Code Format",
		"default"=>'',
		"type"=>"text",
		"requirement"=>array(
			"have_finance"=>true,
		),
	),
	array(
		"name"=>"bank_out_code_format",
		"label"=>"Bank Out Code Format",
		"default"=>'',
		"type"=>"text",
		"requirement"=>array(
			"have_bank_in_out"=>true,
		),
	),
	array(
		"name"=>"bank_in_code_format",
		"label"=>"Bank In Code Format",
		"default"=>'',
		"type"=>"text",
		"requirement"=>array(
			"have_bank_in_out"=>true,
		),
	),
	
	
);