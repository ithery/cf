<?php

$log = array(
	array(
		"name"=>"have_log_login",
		"label"=>"Login Success Log",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_log_login_fail",
		"label"=>"Login Fail Log",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_log_request",
		"label"=>"Request Log",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"log_request"=>true,
		),
	),
	array(
		"name"=>"have_log_activity",
		"label"=>"Activity Log",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_log_print",
		"label"=>"Print Log",
		"default"=>false,
		"type"=>"checkbox",
	),
);
