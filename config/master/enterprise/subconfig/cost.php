<?php

$cost = array(
	array(
		"name"=>"have_cost",
		"label"=>"Have Cost",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_cost_code",
		"label"=>"Have Code",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_cost"=>true,
		),
	),
	array(
		"name"=>"have_purchase_cost",
		"label"=>"Have Purchase Cost",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_cost"=>true,
		),
		
	),
	array(
		"name"=>"have_sales_cost",
		"label"=>"Have Sales Cost",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_cost"=>true,
		),
	),
	
	
);