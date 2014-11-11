<?php


return array(
	array(
		"name"=>"report_resto_list",
		"label"=>"Restaurant Report",
		"requirements"=>array(
			"have_resto_store"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/report/report_resto.php",
	),
	array(
		"name"=>"report_item_list",
		"label"=>"Item Report",
		"subnav"=>include dirname(__FILE__)."/report/report_item.php",
	),//end report_item_list
	array(
		"name"=>"report_purchase_list",
		"label"=>"Purchase Report",
		"requirements"=>array(
			"have_purchase"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/report/report_purchase.php",
	),//end report_purchase_list
	array(
		"name"=>"report_sales_list",
		"label"=>"Sales Report",
		"requirements"=>array(
			"have_sales"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/report/report_sales.php",
	),//end report_sales_list
	array(
		"name"=>"report_payable_list",
		"label"=>"Payable Report",
		"requirements"=>array(
			"have_purchase_credit"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/report/report_payable.php",
	),//end report_purchase_list
	array(
		"name"=>"report_receivable_list",
		"label"=>"Receivable Report",
		"requirements"=>array(
			"have_sales_credit"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/report/report_receivable.php",
	),//end report_sales_list
	array(
		"name"=>"report_accounting_list",
		"label"=>"Accounting Report",
		"requirements"=>array(
			"have_accounting"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/report/report_accounting.php",
	),//end report_accounting
	array(
		"name"=>"report_gross_income",
		"label"=>"Gross Income Report",
		"controller"=>"report_gross_income",
		"method"=>"index",
		'action'=>cnav::report_action('gross_income'),
	),
	array(
		"name"=>"report_cashflow_plain",
		"label"=>"Cashflow Plain Report",
		"controller"=>"report_cashflow_plain",
		"method"=>"index",
		'action'=>cnav::report_action('cashflow_plain'),
	),
	array(
		"name"=>"report_receipt",
		"label"=>"Receipt Report",
		"controller"=>"retail",
		"method"=>"receipt_report",
		"requirements"=>array(
			"printer_type"=>"tmu220",
		),
		'action'=>array(
			array(
				'name'=>'receipt_report_cash',
				'label'=>'Cash',
			),
			array(
				'name'=>'receipt_report_item',
				'label'=>'Item',
			),
			array(
				'name'=>'receipt_report_summary',
				'label'=>'Summary',
			),
		),//end action report_receipt
	),
	array(
		"name"=>"report_expense",
		"label"=>"Expense Report",
		"controller"=>"report_expense",
		"method"=>"index",
		"requirements"=>array(
			"have_expense"=>true,
		),
		"action"=>cnav::report_action('expense'),
		
	),
);
