<?php
return array(
	array(
		"name"=>"payable_purchase_payment",
		"label"=>"Payable Purchase Payment",
		"controller"=>"payable_purchase_payment",
		"method"=>"add",
		
	),
	
	array(
		"name"=>"payable_purchase_list",
		"label"=>"Payable Purchase List",
		"controller"=>"payable_purchase",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_payable_purchase',
				'label'=>'Detail Payable Purchase',
				'controller'=>'payable_purchase',
				'method'=>'detail',
			),
			array(
				'name'=>'write_off_payable_purchase',
				'label'=>'Write Off',
				'controller'=>'payable_purchase',
				'method'=>'write_off',
			),
			array(
				'name'=>'add_purchase_discount',
				'label'=>'Add Discount',
				'controller'=>'payable_purchase',
				'method'=>'add_discount',
				"requirements"=>array(
					"have_purchase_discount"=>true,
				),
			),
			array(
				'name'=>'detail_purchase_discount',
				'label'=>'Detail Discount',
			),
			
		),//end action 
	),
	array(
		"name"=>"payable_purchase_write_off_list",
		"label"=>"Payable Purchase Write Off List",
		"controller"=>"payable_purchase_write_off",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_purchase_write_off',
				'label'=>'Detail Write Off',
				'controller'=>'payable_purchase_write_off',
				'method'=>'detail',
			),
			array(
				'name'=>'delete_purchase_write_off',
				'label'=>'Delete Write Off',
				'controller'=>'payable_purchase_write_off',
				'method'=>'delete',
			),
		),
	),
	array(
		"name"=>"payable_purchase_payment_list",
		"label"=>"Payable Purchase Payment List",
		"controller"=>"payable_purchase_payment",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_purchase_payment',
				'label'=>'Detail Payment',
				'controller'=>'payable_purchase_payment',
				'method'=>'detail',
			),
			array(
				'name'=>'delete_purchase_payment',
				'label'=>'Delete Payment',
				'controller'=>'payable_purchase_payment',
				'method'=>'delete',
			),
		),
	),
	array(
		"name"=>"payable_purchase_discount",
		"label"=>"Payable Purchase Discount List",
		"controller"=>"payable_purchase_discount",
		"method"=>"index",
		"requirements"=>array(
			"have_purchase_discount"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_purchase_discount',
				'label'=>'Detail Purchase Discount',
				'controller'=>'payable_sales_discount',
				'method'=>'detail',
			),
			array(
				'name'=>'print_purchase_discount_bill',
				'label'=>'Print Discount Bill',
				"requirements"=>array(
					"have_purchase_discount_bill"=>true,
				),
			),
			array(
				'name'=>'detail_purchase',
				'label'=>'Detail Purchase',
			),
			array(
				'name'=>'delete_purchase_discount',
				'label'=>'Delete Discount',
				'controller'=>'payable_purchase_discount',
				'method'=>'delete',
			),
		),
	),
	array(
		"name"=>"payable_purchase_giro_payment_list",
		"label"=>"Payable Purchase Giro Payment List",
		"controller"=>"payable_purchase_giro_payment_list",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'approve_payable_purchase_giro_payment',
				'label'=>'Cashing Giro',
				'controller'=>'payable_purchase_giro_payment_list',
				'method'=>'approve',
			),
			array(
				'name'=>'reject_payable_purchase_giro_payment',
				'label'=>'Reject Giro',
				'controller'=>'payable_purchase_giro_payment_list',
				'method'=>'reject',
			),
			
		),//end action purchase_giro_payment 
	),
	array(
		"name"=>"debit_note_menu",
		"label"=>"Debit Note",
		"requirements"=>array(
			"have_debit_note"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/payable_purchase/debit_note.php",
	),
	
);