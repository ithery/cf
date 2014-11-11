<?php

$contact = array(
	array(
		"name"=>"have_supplier",
		"label"=>"Have Supplier",
		"default"=>true,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_customer",
		"label"=>"Have Customer",
		"default"=>true,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_customer_credit_limit",
		"label"=>"Have Customer Credit Limit",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_customer"=>true,
		),
	),
	array(
		"name"=>"have_employee",
		"label"=>"Have Employee",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_expedition",
		"label"=>"Have Expedition",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_salesman",
		"label"=>"Have Salesman",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_employee"=>true,
		),
	),
	array(
		"name"=>"have_salesman_commission",
		"label"=>"Have Salesman Commission",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_salesman"=>true,
		),
	),
	array(
		"name"=>"have_doctor",
		"label"=>"Have Doctor",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_business_partner",
		"label"=>"Have Business Partner",
		"default"=>false,
		"type"=>"checkbox",
	),
);