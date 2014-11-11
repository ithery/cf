<?php

$sales = array(
	array(
		"name"=>"have_sales",
		"label"=>"Have Sales",
		"default"=>false,
		"type"=>"checkbox",
		
	),
    array(
        "name"=>"have_sales_due_date",
        "label"=>"Sales Due Date",
        "default"=>false,
        "type"=>"checkbox",
        "requirement"=>array(
            "have_sales"=>true,
        ),
    ),
	array(
		"name"=>"sales_order",
		"label"=>"Have Sales Order",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_ui",
		"label"=>"Sales UI",
		"default"=>'table',
		"type"=>"select",
		"list"=>array(
			"table"=>"Table",
			"carousel"=>"Carousel",
		),
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_filter_select_ui",
		"label"=>"Sales Filter Select UI",
		"default"=>'select',
		"type"=>"select",
		"list"=>array(
			"select"=>"Select",
			"button"=>"Button",
		),
		"requirement"=>array(
			"sales_ui"=>"carousel",
		),
	),
	
	array(
		"name"=>"have_sales_customer",
		"label"=>"Using Customer",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_customer"=>true,
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_salesman",
		"label"=>"Using Salesman",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_salesman"=>true,
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_expedition",
		"label"=>"Using Expedition",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_expedition"=>true,
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_credit",
		"label"=>"Have Sales Credit",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_save_last_price",
		"label"=>"Have Sales Save Last Price",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_change_price",
		"label"=>"Sales Change Price",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_change_qty",
		"label"=>"Sales Change Qty",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_item_rounded",
		"label"=>"Sales Item Rounded",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_item_rounded_value",
		"label"=>"Sales Item Rounded Value",
		"default"=>'500',
		"type"=>"text",
		"requirement"=>array(
			"sales_item_rounded"=>true,
		),
	),
	array(
		"name"=>"edit_sales",
		"label"=>"Edit Sales",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_code_format",
		"label"=>"Sales Code Format",
		"default"=>'',
		"type"=>"text",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_change_title",
		"label"=>"Sales Change Title",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_bill",
		"label"=>"Have Sales Bill",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_bill_print_mode",
		"label"=>"Sales Bill Print Mode",
		"default"=>'server',
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"have_sales_bill"=>true,
		),
	),
	array(
		"name"=>"sales_bill_print_after_checkout",
		"label"=>"Sales Bill Print After Checkout",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_bill"=>true,
		),
	),
	array(
		"name"=>"sales_bill_org",
		"label"=>"Sales Bill Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_bill"=>true,
		),
	),

	array(
		"name"=>"have_sales_waybill",
		"label"=>"Have Sales Waybill",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"sales_waybill_print_mode",
		"label"=>"Sales Waybill Print Mode",
		"default"=>false,
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"have_sales_waybill"=>true,
		),
	),
	array(
		"name"=>"sales_waybill_print_after_checkout",
		"label"=>"Sales Waybill Print After Checkout",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_waybill"=>true,
		),
	),
	array(
		"name"=>"sales_waybill_org",
		"label"=>"Sales Waybill Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_waybill"=>true,
		),
	),
	array(
		"name"=>"have_custom_sales_waybill",
		"label"=>"Have Custom Sales Waybill",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_waybill"=>true,
		),
	),
	array(
		"name"=>"have_sales_receipt",
		"label"=>"Have Sales Receipt",
		"default"=>'',
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_landed_cost",
		"label"=>"Have Sales Landed Cost",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_detail_landed_cost",
		"label"=>"Have Sales Detail Landed Cost",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_receivable_generate",
		"label"=>"Have Receivable Generate",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_receipt_bill",
		"label"=>"Have Sales Receipt Bill",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_receipt"=>true,
		),
	),
	array(
		"name"=>"sales_receipt_bill_print_mode",
		"label"=>"Sales Receipt Bill Print Mode",
		"default"=>false,
		"type"=>"select",
		"list"=>array(
			"server"=>"Server",
			"client"=>"Client",
		),
		"requirement"=>array(
			"have_sales_receipt_bill"=>true,
		),
	),
	
	array(
		"name"=>"sales_receipt_bill_org",
		"label"=>"Sales Receipt Bill Org Custom",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_receipt_bill"=>true,
		),
	),
	array(
		"name"=>"have_sales_refund",
		"label"=>"Have Sales Refund",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	
	array(
		"name"=>"have_credit_note",
		"label"=>"Have Credit Note",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_create_credit_note",
		"label"=>"Have Create Credit Note",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_credit_note"=>true,
		),
	),
	array(
		"name"=>"have_sales_overpayment",
		"label"=>"Have Sales Overpayment",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
			"have_sales_credit"=>true,
		),
	),
	array(
		"name"=>"have_produce_credit_note_on_sales_overpayment",
		"label"=>"Produce Credit Note On Over Payment",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales_overpayment"=>true,
			"have_credit_note"=>true,
		),
	),
	array(
		"name"=>"have_sales_revision",
		"label"=>"Have Sales Revision",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_item_duplicate",
		"label"=>"Sales Item Duplicate",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_payment_amount",
		"label"=>"Have Sales Payment Amount",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_date",
		"label"=>"Have Sales Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_sales_refund_date",
		"label"=>"Have Sales Refund Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
			"have_sales_refund"=>true,
		),
	),
	array(
		"name"=>"have_sales_payment_date",
		"label"=>"Have Sales Payment Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_sales"=>true,
		),
	),
	array(
		"name"=>"have_credit_note_write_off_date",
		"label"=>"Have Credit Note Write Off Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_credit_note"=>true,
		),
	),
	array(
		"name"=>"have_credit_note_cashing_date",
		"label"=>"Have Credit Note Cashing Date",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_credit_note"=>true,
		),
	),
	
	
);