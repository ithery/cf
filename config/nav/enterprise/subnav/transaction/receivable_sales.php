<?php
return array(
	array(
		"name"=>"receivable_sales_payment",
		"label"=>"Receivable Sales Payment",
		"controller"=>"receivable_sales_payment",
		"method"=>"add",
		
	),
	array(
		"name"=>"receivable_sales_list",
		"label"=>"Receivable Sales List",
		"controller"=>"receivable_sales",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_receivable_sales',
				'label'=>'Detail Receivable Sales',
				'controller'=>'receivable_sales',
				'method'=>'detail',
			),
			array(
				'name'=>'write_off_receivable_sales',
				'label'=>'Write Off',
				'controller'=>'receivable_sales',
				'method'=>'write_off',
			),
			array(
				'name'=>'add_sales_discount',
				'label'=>'Add Discount',
				'controller'=>'receivable_sales',
				'method'=>'add_discount',
				"requirements"=>array(
					"have_sales_discount"=>true,
				),
			),
			array(
				'name'=>'detail_sales_discount',
				'label'=>'Detail Discount',
			),
			
		),//end action sales_payment
	),
	array(
		"name"=>"receivable_sales_write_off_list",
		"label"=>"Receivable Sales Write Off List",
		"controller"=>"receivable_sales_write_off",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_sales_write_off',
				'label'=>'Detail Write Off',
				'controller'=>'receivable_sales_write_off',
				'method'=>'detail',
			),
			array(
				'name'=>'delete_sales_write_off',
				'label'=>'Delete Write Off',
				'controller'=>'receivable_sales_write_off',
				'method'=>'delete',
			),
		),
	),
	array(
		"name"=>"receivable_sales_payment_list",
		"label"=>"Receivable Sales Payment List",
		"controller"=>"receivable_sales_payment",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_sales_payment',
				'label'=>'Detail Payment',
				'controller'=>'receivable_sales_payment',
				'method'=>'detail',
			),
			array(
				'name'=>'delete_sales_payment',
				'label'=>'Delete Payment',
				'controller'=>'receivable_sales_payment',
				'method'=>'delete',
			),
		),
	),
	array(
		"name"=>"receivable_sales_discount",
		"label"=>"Receivable Sales Discount List",
		"controller"=>"receivable_sales_discount",
		"method"=>"index",
		"requirements"=>array(
			"have_sales_discount"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_sales_discount',
				'label'=>'Detail Sales Discount',
				'controller'=>'receivable_sales_discount',
				'method'=>'detail',
			),
			array(
				'name'=>'detail_sales',
				'label'=>'Detail Sales',
			),
			array(
				'name'=>'print_sales_discount_bill',
				'label'=>'Print Discount Bill',
				"requirements"=>array(
					"have_sales_discount_bill"=>true,
				),
			),
			
			array(
				'name'=>'delete_sales_discount',
				'label'=>'Delete Discount',
				'controller'=>'receivable_sales_discount',
				'method'=>'delete',
			),
		),
	),
	array(
		"name"=>"receivable_sales_giro_payment_list",
		"label"=>"Receivable Sales Giro Payment List",
		"controller"=>"receivable_sales_giro_payment_list",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'approve_receivable_sales_giro_payment',
				'label'=>'Cashing Giro',
				'controller'=>'receivable_sales_giro_payment_list',
				'method'=>'approve',
			),
			array(
				'name'=>'reject_receivable_sales_giro_payment',
				'label'=>'Reject Giro',
				'controller'=>'receivable_sales_giro_payment_list',
				'method'=>'reject',
			),
			
		),//end action sales_giro_payment 
	),
	array(
		"name"=>"credit_note_menu",
		"label"=>"Credit Note",
		"requirements"=>array(
			"have_credit_note"=>true,
		),
		"subnav"=>include dirname(__FILE__)."/receivable_sales/credit_note.php",
	),
	
);