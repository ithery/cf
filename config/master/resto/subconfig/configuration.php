<?php

$configuration = array(
	array(
		"name"=>"update_last_request",
		"label"=>"Update User Last Request",
		"default"=>true,
		"type"=>"checkbox",
		"help"=>"* this will know current user login"
	),
	array(
		"name"=>"log_request",
		"label"=>"Log All Request",
		"default"=>true,
		"type"=>"checkbox",
		"help"=>"* this will log each request, maybe can slow down the performance"
	),

);