<?php
include dirname(__FILE__).DIRECTORY_SEPARATOR."subconfig".DIRECTORY_SEPARATOR."global.php";
include dirname(__FILE__)."/subconfig/client_server.php";
include dirname(__FILE__)."/subconfig/configuration.php";

$config=array(
	array(
		"name"=>"global_setting",
		"label"=>"Global",
		"config"=>$global,
	),
	array(
		"name"=>"client_server_setting",
		"label"=>"Client Server",
		"config"=>$client_server,
	),
	
	array(
		"name"=>"configuration_setting",
		"label"=>"Configuration",
		"config"=>$configuration,
	),
	array(
		"name"=>"resto_setting",
		"label"=>"Restaurant",
		"config"=>array(
			array(
				"name"=>"have_resto_table",
				"label"=>"Have Table",
				"default"=>true,
				"type"=>"checkbox",
			),
			array(
				"name"=>"have_resto_floor",
				"label"=>"Have Floor",
				"default"=>true,
				"type"=>"checkbox",
				"requirement"=>array(
					"have_resto_table"=>true,
				),
			),
			
			array(
				"name"=>"resto_transaction_code_format",
				"label"=>"Resto Transaction Code Format",
				"default"=>true,
				"type"=>"text",
			),
			
			array(
				"name"=>"resto_default_tax_percent",
				"label"=>"Resto Default Tax (%)",
				"default"=>"10",
				"type"=>"text",
				
			),
			array(
				"name"=>"resto_default_service_charge_percent",
				"label"=>"Resto Default Service Charge (%)",
				"default"=>"10",
				"type"=>"text",
			),
			array(
				"name"=>"have_resto_multiple_payment",
				"label"=>"Have Multiple Payment",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_resto_auto_print_bill",
				"label"=>"Auto Print Bill",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_resto_auto_print_checker",
				"label"=>"Auto Print Checker",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_resto_auto_print_checker_to_kitchen",
				"label"=>"Auto Print Checker to Kitchen",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_resto_manual_discount",
				"label"=>"Manual Discount",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_resto_automatic_promo",
				"label"=>"Automatic Promo",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_resto_show_all_promo",
				"label"=>"Show All Promo",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_payment_amount_keyboard",
				"label"=>"Virtual Keyboard Amount",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"resto_order_filter_select_ui",
				"label"=>"Order Filter UI",
				"default"=>"button",
				"type"=>"select",
				"list"=>array(
					"select"=>"Select",
					"button"=>"Button",
				),
				
			),
			array(
				"name"=>"resto_single_button",
				"label"=>"Single Button To Next",
				"default"=>true,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_resto_payment_amount_digit",
				"label"=>"Have Payment Digit",
				"default"=>false,
				"type"=>"checkbox",
				
			),
			array(
				"name"=>"have_show_cash_change",
				"label"=>"Have Show Cash Change",
				"default"=>false,
				"type"=>"checkbox",
				
			),
			
		),
	),
	array(
		"name"=>"receipt_setting",
		"label"=>"Receipt",
		"config"=>array(
			array(
				"name"=>"printer_type",
				"label"=>"Default Printer Type",
				"default"=>"tmu220",
				"type"=>"select",
				"list"=>array(
					"tmu220"=>"EPSON TM-U220",
					"lx300"=>"EPSON LX-300",
				),
			),
			array(
				"name"=>"printer_protocol_name",
				"label"=>"Printer Protocol Name",
				"default"=>"cwebrawprint",
				"type"=>"text",
				"help"=>"* leave blank will default to cwebrawprint protocol"
				
			),
			
			array(
				"name"=>"server_printer_name",
				"label"=>"Server Printer Name",
				"default"=>false,
				"type"=>"text",
				"help"=>"* leave blank if all printer mode is client"
			),
			
			array(
				"name"=>"print_cash_drawer",
				"label"=>"Have Print Cash Drawer ",
				"default"=>false,
				"type"=>"checkbox",
			),
			array(
				"name"=>"bill_bold_title",
				"label"=>"Bill Bold Title",
				"default"=>false,
				"type"=>"checkbox",
			),
			array(
				"name"=>"print_cash_drawer_esc_code",
				"label"=>"Print Cash Drawer Esc Code",
				"default"=>false,
				"type"=>"text",
				"requirement"=>array(
					"print_cash_drawer"=>true,
				),
			),
			
			array(
				"name"=>"print_auto_cutter",
				"label"=>"Have Print Auto Cutter",
				"default"=>false,
				"type"=>"checkbox",
			),
			
			array(
				"name"=>"print_auto_cutter_esc_code",
				"label"=>"Print Auto Cutter Esc Code",
				"default"=>'',
				"type"=>"text",
				"requirement"=>array(
					"print_auto_cutter"=>true,
				),
			),
			array(
				"name"=>"receiptline",
				"label"=>"Recipt Line",
				"default"=>"0",
				"type"=>"select",
				"list"=>array(
					"0"=>"0",
					"1"=>"1",
					"2"=>"2",
					"3"=>"3",
					"4"=>"4",
					"5"=>"5",
					"6"=>"6",
					"7"=>"7",
					"8"=>"8",
					"9"=>"9",
				),
			),
			array(
				"name"=>"bill_footer_1",
				"label"=>"Bill Footer 1",
				"default"=>'',
				"type"=>"text",
			),
			array(
				"name"=>"bill_footer_2",
				"label"=>"Bill Footer 2",
				"default"=>'',
				"type"=>"text",
			),
			array(
				"name"=>"bill_footer_3",
				"label"=>"Bill Footer 3",
				"default"=>'',
				"type"=>"text",
			),
			array(
				"name"=>"bill_footer_4",
				"label"=>"Bill Footer 4",
				"default"=>'',
				"type"=>"text",
			),
			array(
				"name"=>"bill_footer_5",
				"label"=>"Bill Footer 5",
				"default"=>'',
				"type"=>"text",
			),
			
		),
	),
	
);


unset($global);
unset($client_server);
unset($configuration);


