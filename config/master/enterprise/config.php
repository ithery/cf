<?php
include dirname(__FILE__)."/subconfig/global.php";
include dirname(__FILE__)."/subconfig/client_server.php";
include dirname(__FILE__)."/subconfig/configuration.php";
include dirname(__FILE__)."/subconfig/contact.php";
include dirname(__FILE__)."/subconfig/warehouse.php";
include dirname(__FILE__)."/subconfig/item.php";
include dirname(__FILE__)."/subconfig/stock_opname.php";
include dirname(__FILE__)."/subconfig/bom.php";
include dirname(__FILE__)."/subconfig/production.php";
include dirname(__FILE__)."/subconfig/cost.php";
include dirname(__FILE__)."/subconfig/purchase.php";
include dirname(__FILE__)."/subconfig/sales.php";
include dirname(__FILE__)."/subconfig/purchase_return.php";
include dirname(__FILE__)."/subconfig/sales_return.php";
include dirname(__FILE__)."/subconfig/purchase_discount.php";
include dirname(__FILE__)."/subconfig/sales_discount.php";
include dirname(__FILE__)."/subconfig/expense.php";

include dirname(__FILE__)."/subconfig/analyze.php";
include dirname(__FILE__)."/subconfig/log.php";
include dirname(__FILE__)."/subconfig/dashboard.php";
include dirname(__FILE__)."/subconfig/report.php";

include dirname(__FILE__)."/subconfig/finance.php";
include dirname(__FILE__)."/subconfig/accounting.php";

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
		"name"=>"contact_setting",
		"label"=>"Contact",
		"config"=>$contact,
	),
	array(
		"name"=>"warehouse_setting",
		"label"=>"Warehouse",
		"config"=>$warehouse,
	),
	array(
		"name"=>"item_setting",
		"label"=>"Item",
		"config"=>$item,
	),
	array(
		"name"=>"stock_opname_setting",
		"label"=>"Stock Opname",
		"config"=>$stock_opname,
	),
	array(
		"name"=>"bom_setting",
		"label"=>"BOM",
		"config"=>$bom,
	),
	array(
		"name"=>"production_setting",
		"label"=>"Production",
		"config"=>$production,
	),
	array(
		"name"=>"cost_setting",
		"label"=>"Cost",
		"config"=>$cost,
	),	
	array(
		"name"=>"purchase_setting",
		"label"=>"Purchase",
		"config"=>$purchase,
	),
	array(
		"name"=>"sales_setting",
		"label"=>"Sales",
		"config"=>$sales,
	),
	array(
		"name"=>"purchase_return_setting",
		"label"=>"Purchase Return",
		"config"=>$purchase_return,
		
	),
	array(
		"name"=>"sales_return_setting",
		"label"=>"Sales Return",
		"config"=>$sales_return,
	),
	array(
		"name"=>"purchase_discount_setting",
		"label"=>"Purchase Discount",
		"config"=>$purchase_discount,
		
	),
	array(
		"name"=>"sales_discount_setting",
		"label"=>"Sales Discount",
		"config"=>$sales_discount,
		
	),		
	array(
		"name"=>"expense_setting",
		"label"=>"Expense",
		"config"=>$expense,
		
	),		
		
	// array(
		// "name"=>"payable_other_setting",
		// "label"=>"Payable Other",
		// "config"=>array(
			// array(
				// "name"=>"have_payable_other",
				// "label"=>"Have Payable Other",
				// "default"=>false,
				// "type"=>"checkbox",
				
			// ),
			// array(
				// "name"=>"payable_other_customer",
				// "label"=>"Have Customer",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_payable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"payable_other_supplier",
				// "label"=>"Have Supplier",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_payable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"payable_other_employee",
				// "label"=>"Have Employee",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_payable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"payable_other_business_partner",
				// "label"=>"Have Business Partner",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_payable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"have_produce_debit_note_on_payable_supplier",
				// "label"=>"Produce Debit Note On Payable Supplier",
				// "default"=>'',
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_debit_note"=>true,
					// "have_payable_other"=>true,
				// ),
			// ),
		// ),
	// ),
	// array(
		// "name"=>"receivable_other_setting",
		// "label"=>"Receivable Other",
		// "config"=>array(
			// array(
				// "name"=>"have_receivable_other",
				// "label"=>"Have Receivable Other",
				// "default"=>false,
				// "type"=>"checkbox",
				
			// ),
			// array(
				// "name"=>"receivable_other_customer",
				// "label"=>"Have Customer",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_receivable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"receivable_other_supplier",
				// "label"=>"Have Supplier",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_receivable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"receivable_other_employee",
				// "label"=>"Have Employee",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_receivable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"receivable_other_business_partner",
				// "label"=>"Have Business Partner",
				// "default"=>false,
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_receivable_other"=>true,
				// ),
			// ),
			// array(
				// "name"=>"have_produce_credit_note_on_receivable_customer",
				// "label"=>"Produce Credit Note On Receivable Customer",
				// "default"=>'',
				// "type"=>"checkbox",
				// "requirement"=>array(
					// "have_debit_note"=>true,
					// "have_receivable_other"=>true,
				// ),
			// ),
		// ),
	// ),
	array(
		"name"=>"accounting_setting",
		"label"=>"Accounting",
		"config"=>$accounting,
	),
	array(
		"name"=>"finance_setting",
		"label"=>"Finance",
		"config"=>$finance,
	),
	
	array(
		"name"=>"payment_type_setting",
		"label"=>"Payment Type",
		"config"=>array(
			array(
				"name"=>"payment_type_purchase_cash",
				"label"=>"Purchase Cash",
				"default"=>array(),
				"type"=>"select",
				"multiple"=>true,
				"list"=>array(
					"cash"=>"CASH",
					"bank_transfer"=>"BANK TRANSFER",
					"giro"=>"GIRO",
					"payable_other"=>"PAYABLE OTHER",
				)
			),
			array(
				"name"=>"payment_type_purchase_credit",
				"label"=>"Purchase Credit",
				"default"=>array(),
				"type"=>"select",
				"multiple"=>true,
				"list"=>array(
					"cash"=>"CASH",
					"bank_transfer"=>"BANK TRANSFER",
					"giro"=>"GIRO",
					"payable_other"=>"PAYABLE OTHER",
				),
				"requirement"=>array(
					"have_purchase_credit"=>true,
				),
			),
			array(
				"name"=>"payment_type_sales_cash",
				"label"=>"Sales Cash",
				"default"=>array(),
				"type"=>"select",
				"multiple"=>true,
				"list"=>array(
					"cash"=>"CASH",
					"bank_transfer"=>"BANK TRANSFER",
					"giro"=>"GIRO",
				)
			),
			array(
				"name"=>"payment_type_sales_credit",
				"label"=>"Sales Credit",
				"default"=>array(),
				"type"=>"select",
				"multiple"=>true,
				"list"=>array(
					"cash"=>"CASH",
					"bank_transfer"=>"BANK TRANSFER",
					"giro"=>"GIRO",
				),
				"requirement"=>array(
					"have_sales_credit"=>true,
				),
			),
			array(
				"name"=>"payment_type_payable",
				"label"=>"Payable",
				"default"=>array(),
				"type"=>"select",
				"multiple"=>true,
				"list"=>array(
					"cash"=>"CASH",
					"bank_transfer"=>"BANK TRANSFER",
					"giro"=>"GIRO",
				)
			),
			array(
				"name"=>"payment_type_receivable",
				"label"=>"Receivable",
				"default"=>array(),
				"type"=>"select",
				"multiple"=>true,
				"list"=>array(
					"cash"=>"CASH",
					"bank_transfer"=>"BANK TRANSFER",
					"giro"=>"GIRO",
				)
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

		),
	),
	array(
		"name"=>"tmu220_printer_setting",
		"label"=>"TM U-220 Printer Setting",
		"config"=>array(
			
			array(
				"name"=>"bill_header_1",
				"label"=>"Header Bill 1",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_header_2",
				"label"=>"Header Bill 2",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_header_3",
				"label"=>"Header Bill 3",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_header_4",
				"label"=>"Header Bill 4",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_header_5",
				"label"=>"Header Bill 5",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_footer_1",
				"label"=>"Footer Bill 1",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_footer_2",
				"label"=>"Footer Bill 2",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_footer_3",
				"label"=>"Footer Bill 3",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_footer_4",
				"label"=>"Footer Bill 4",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"bill_footer_5",
				"label"=>"Footer Bill 5",
				"default"=>"",
				"type"=>"text",
				
			),
			array(
				"name"=>"receiptline",
				"label"=>"Receipt Line",
				"default"=>"5",
				"type"=>"text",
				
			),

			

		),
	),
	array(
		"name"=>"report_setting",
		"label"=>"Report",
		"config"=>$report,
		
	),
	array(
		"name"=>"analyze_setting",
		"label"=>"Analyze",
		"config"=>$analyze,
	),
	array(
		"name"=>"log_setting",
		"label"=>"Log",
		"config"=>$log,
		
	),
	
	array(
		"name"=>"dashboard_setting",
		"label"=>"Dashboard",
		"config"=>$dashboard,
		
	),
	
	array(
		"name"=>"misc_setting",
		"label"=>"Misc",
		"config"=>array(
			
		),
	),
	
	
);


unset($global);
unset($client_server);
unset($configuration);
unset($contact);
unset($warehouse);
unset($item);
unset($bom);
unset($production);
unset($cost);
unset($purchase);
unset($sales);
unset($purchase_return);
unset($sales_return);
unset($purchase_discount);
unset($sales_discount);
unset($expense);

unset($analyze);
unset($log);
unset($dashboard);
unset($report);
unset($finance);
unset($accounting);

return $config;
