<?php

$global = array(
	array(
		"name"=>"app_id",
		"label"=>"Application ID",
		"default"=>"1",
		"type"=>"text",
	),
	array(
		"name"=>"title",
		"label"=>"Title",
		"default"=>"Cresenity",
		"type"=>"text",
	),
	array(
		"name"=>"admin_email",
		"label"=>"Administrator Email",
		"default"=>'contact@cresenitytech.com',
		"type"=>"text",
	),
	array(
		"name"=>"multilang",
		"label"=>"Multi Language",
		"default"=>true,
		"type"=>"checkbox",
		
	),
	array(
		"name"=>"lang",
		"label"=>"Default Language",
		"default"=>"id",
		"type"=>"select",
		"list"=>array(
			"en"=>"English",
			"id"=>"Indonesia",
		),
		"requirement"=>array(
			"multilang"=>true,
		),
	),
	array(
		"name"=>"set_timezone",
		"label"=>"Set Timezone",
		"default"=>true,
		"type"=>"checkbox",
	),
	array(
		"name"=>"default_timezone",
		"label"=>"Default Timezone",
		"default"=>true,
		"type"=>"select",
		"list"=>ctimezone::timezone_list(),
		"requirement"=>array(
			"set_timezone"=>true,
		),
	),
	array(
		"name"=>"have_clock",
		"label"=>"Have Clock",
		"default"=>false,
		"type"=>"checkbox",
		
	),
	array(
		"name"=>"top_menu_cashier",
		"label"=>"Have Cashier Top Menu",
		"default"=>true,
		"type"=>"checkbox",
		"help"=>"* Check this menu to create shortcut command to sales page",
	),
	
	array(
		"name"=>"mail_error",
		"label"=>"Mail Error to Administrator Email",
		"default"=>true,
		"type"=>"checkbox",
		"help"=>"* Check this menu for email error to administrator email (smtp must enabled)",
	),
	array(
		"name"=>"mail_error_smtp",
		"label"=>"Mail Error to Administrator Email SMTP",
		"default"=>true,
		"type"=>"checkbox",
		"requirement"=>array(
			"mail_error"=>true,
		),
	),
	
	array(
		"name"=>"date_formatted",
		"label"=>"Date Format",
		"default"=>"Y-m-d",
		"type"=>"text",
	),
	array(
		"name"=>"time_formatted",
		"label"=>"Time Format",
		"default"=>"H:i:s",
		"type"=>"text",
	),
	array(
		"name"=>"long_date_formatted",
		"label"=>"Long Date Format",
		"default"=>"Y-m-d H:i:s",
		"type"=>"text",
	),
	array(
		"name"=>"smtp_host",
		"label"=>"SMTP Host",
		"default"=>"",
		"type"=>"text",
	),
	array(
		"name"=>"smtp_port",
		"label"=>"SMTP Port",
		"default"=>"",
		"type"=>"text",
	),
	array(
		"name"=>"smtp_secure",
		"label"=>"SMTP Use SSL",
		"default"=>"",
		"type"=>"select",
		"list"=>array(
			""=>"NONE",
			"ssl"=>"SSL",
			"tls"=>"TLS",
			
		)
		
	),
	array(
		"name"=>"smtp_username",
		"label"=>"SMTP Username",
		"default"=>"",
		"type"=>"text",
	),
	array(
		"name"=>"smtp_password",
		"label"=>"SMTP Password",
		"default"=>"",
		"type"=>"text",
	),
	array(
		"name"=>"smtp_from",
		"label"=>"SMTP From",
		"default"=>"",
		"type"=>"text",
	),
	
	
	
);