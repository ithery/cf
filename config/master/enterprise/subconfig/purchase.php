<?php

$purchase = array(
	array(
		"name"=>"have_purchase",
		"label"=>"Have Purchase",
		"default"=>false,
		"type"=>"checkbox",
		
	),
	array(
		"name"=>"have_purchase_due_date",
		"label"=>"Purchase Due Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"purchase_order",
		"label"=>"Have Purchase Order",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_date_purchase_order",
		"label"=>"Have Purchase Order Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_order"=>true,
		),
	),
	array(
		"name"=>"have_purchase_order_bill",
		"label"=>"Have Purchase Order Bill",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_order"=>true,
		),
	),
	array(
		"name"=>"have_receiving",
		"label"=>"Have Receiving",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	
	array(
		"name"=>"purchase_order_print_mode",
		"label"=>"Purchase Order Print Mode",
		"default"=>'server',
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"have_purchase_order_bill"=>true,
		),
	),
	array(
		"name"=>"purchase_order_print_after_checkout",
		"label"=>"Purchase Order Print After Checkout",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"purchase_order_print_mode"=>"server",
		),
	),
	array(
		"name"=>"purchase_order_code_format",
		"label"=>"Purchase Order Code Format",
		"default"=>'',
		"type"=>"text",
		"requirement"=>array(
			"purchase_order"=>true,
		),
	),
	array(
		"name"=>"purchase_order_bill_org",
		"label"=>"Purchase Order Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase_order_bill"=>true,
		),
	),
	array(
		"name"=>"have_purchase_supplier",
		"label"=>"Using Supplier",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_supplier"=>true,
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_expedition",
		"label"=>"Using Expedition",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_expedition"=>true,
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_credit",
		"label"=>"Have Purchase Credit",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_save_last_price",
		"label"=>"Have Purchase Save Last Price",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"purchase_code_format",
		"label"=>"Purchase Code Format",
		"default"=>'',
		"type"=>"text",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_receipt",
		"label"=>"Have Purchase Receipt",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_landed_cost",
		"label"=>"Have Purchase Landed Cost",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_detail_landed_cost",
		"label"=>"Have Purchase Detail Landed Cost",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_calculate_landed_cost_on_cogs",
		"label"=>"Have Calculate Landed Cost On COGS",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase_detail_landed_cost"=>true,
		),
	),
	array(
		"name"=>"have_payable_generate",
		"label"=>"Have Payable Generate",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_refund",
		"label"=>"Have Purchase Refund",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_debit_note",
		"label"=>"Have Debit Note",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_create_debit_note",
		"label"=>"Have Create Debit Note",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_debit_note"=>true,
		),
	),
	array(
		"name"=>"have_purchase_overpayment",
		"label"=>"Have Purchase Overpayment",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
			"have_purchase_credit"=>true,
		),
	),
	array(
		"name"=>"have_produce_debit_note_on_purchase_overpayment",
		"label"=>"Produce Debit Note On Over Payment",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase_overpayment"=>true,
			"have_debit_note"=>true,
		),
	),
	array(
		"name"=>"have_purchase_revision",
		"label"=>"Have Purchase Revision",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_item_duplicate",
		"label"=>"Purchase Item Duplicate",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_date",
		"label"=>"Purchase Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_purchase_refund_date",
		"label"=>"Have Purchase Refund Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
			"have_purchase_refund"=>true,
		),
	),
	array(
		"name"=>"have_purchase_payment_date",
		"label"=>"Have Purchase Payment Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_purchase"=>true,
		),
	),
	array(
		"name"=>"have_payable_generate_payment_date",
		"label"=>"Have Payable Generate Payment Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_payable_generate"=>true,
		),
	),
	array(
		"name"=>"have_debit_note_write_off_date",
		"label"=>"Have Debit Note Write Off Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_debit_note"=>true,
		),
	),
	array(
		"name"=>"have_debit_note_cashing_date",
		"label"=>"Have Debit Note Cashing Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_debit_note"=>true,
		),
	),
	
	
);