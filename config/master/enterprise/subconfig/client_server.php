<?php

$client_server = array(
	array(
		"name"=>"have_store",
		"label"=>"Have Store",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_resto_store",
		"label"=>"Have Resto Store",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_store"=>true,
		),
	),
	array(
		"name"=>"have_retail_store",
		"label"=>"Have Retail Store",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_store"=>true,
		),
	),
	array(
		"name"=>"have_hotel_store",
		"label"=>"Have Hotel Store",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_store"=>true,
		),
	),
);