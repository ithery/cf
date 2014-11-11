<?php

$client_server = array(
	array(
		"name"=>"have_remote_server",
		"label"=>"Using Remote Server",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"remote_server_protocol",
		"label"=>"Server Protocol",
		"default"=>"http",
		"type"=>"select",
		"list"=>array(
			"http"=>"http",
			"https"=>"https",
		),
		"requirement"=>array(
			"have_remote_server"=>true,
		),
	),
	array(
		"name"=>"remote_server_address",
		"label"=>"Server Address",
		"default"=>"localhost",
		"type"=>"text",
		"requirement"=>array(
			"have_remote_server"=>true,
		),
	),
	array(
		"name"=>"remote_server_port",
		"label"=>"Server Port",
		"default"=>"80",
		"type"=>"text",
		"requirement"=>array(
			"have_remote_server"=>true,
		),
	),
    array(
        "name"=>"have_synchronize",
        "label"=>"Have Synchronize",
        "default"=>false,
        "type"=>"checkbox",
    ),
    array(
        "name"=>"server_synchronize_url",
        "label"=>"Server Synchronize Url",
        "default"=>"",
        "requirement"=>array(
            "have_synchronize"=>true,
        ),
        "type"=>"text",
    ),
	array(
        "name"=>"have_delete_resto_transaction_after_synchronize",
        "label"=>"Delete Transaction After Synchronize",
        "default"=>"",
        "requirement"=>array(
            "have_synchronize"=>true,
        ),
        "type"=>"checkbox",
    ),
	array(
        "name"=>"synchronize_record_count",
        "label"=>"Synchronize_record_count",
        "default"=>"20",
		"list"=>array(
			"10"=>"10",
			"20"=>"20",
			"30"=>"30",
			"40"=>"40",
			"50"=>"50",
		),
        "requirement"=>array(
            "have_synchronize"=>true,
        ),
        "type"=>"select",
    ),

);