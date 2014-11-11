<?php

$bom = array(
	array(
		"name"=>"have_bom",
		"label"=>"Have BOM",
		"default"=>false,
		"type"=>"checkbox",
		
	),
	array(
		"name"=>"bom_code",
		"label"=>"BOM Have Code",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_bom"=>true,
		),
	),
	array(
		"name"=>"bom_code_auto",
		"label"=>"BOM Auto Code",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"bom_code"=>true,
		),
	),
	array(
		"name"=>"bom_have_unit",
		"label"=>"Using Unit",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_unit"=>true,
			"have_bom"=>true,
		),
	),
	array(
		"name"=>"bom_have_unit_output",
		"label"=>"Using Unit Output",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"bom_have_unit"=>true,
		),
	),
	array(
		"name"=>"bom_have_unit_conversion",
		"label"=>"Use Unit Conversion",
		"default"=>true,
		"type"=>"checkbox",
		"requirement"=>array(
			"bom_have_unit"=>true,
		),
	),
	
	array(
		"name"=>"bom_ajax",
		"label"=>"Using ajax to display bom list ",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_bom"=>true,
		),
		
	)
);