<?php
return array(
		array(
			"name"=>"cash_transfer",
			"label"=>"Cash Transfer",
			"controller"=>"cash_transfer",
			"method"=>"index",
		),
		array(
			"name"=>"cash_in",
			"label"=>"Cash In",
			"controller"=>"cash_in",
			"method"=>"index",
		),
		array(
			"name"=>"cash_out",
			"label"=>"Cash Out",
			"controller"=>"cash_out",
			"method"=>"index",
		),
		array(
			"name"=>"cashflow_register",
			"label"=>"Cashflow Register",
			"subnav"=>array(
				array(
					"name"=>"cash_register",
					"label"=>"Cash Register",
					"controller"=>"cashflow_register",
					"method"=>"cash",
				),
				array(
					"name"=>"bank_register",
					"label"=>"Bank Register",
					"controller"=>"cashflow_register",
					"method"=>"bank",
				),
				array(
					"name"=>"giro_register",
					"label"=>"Giro Register",
					"controller"=>"cashflow_register",
					"method"=>"giro",
				),
				array(
					"name"=>"giro_deposited_register",
					"label"=>"Giro Deposited Register",
					"controller"=>"cashflow_register",
					"method"=>"giro_deposited",
				),
				array(
					"name"=>"giro_rejected_register",
					"label"=>"Giro Rejected Register",
					"controller"=>"cashflow_register",
					"method"=>"giro_rejected",
				),
				
			),
		),
		array(
			"name"=>"giro_menu",
			"label"=>"Giro",
			"subnav"=>array(
				array(
					"name"=>"giro_list",
					"label"=>"Daftar Giro",
					"controller"=>"giro",
					"method"=>"index",
				),
			),
		),
		array(
			"name"=>"daily_cash_report",
			"label"=>"Daily Cash Report",
			"controller"=>"daily_cash_report",
			"method"=>"index",
		),
		
		array(
			"name"=>"cashflow",
			"label"=>"Cashflow",
			"controller"=>"cashflow",
			"method"=>"index",
		),
	);
	