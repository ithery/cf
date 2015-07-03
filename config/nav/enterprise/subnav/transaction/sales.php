<?php
return array(
	array(
		"name"=>"sales",
		"label"=>"Sales",
		"controller"=>"retail",
		"method"=>"sales",
	),
	array(
		"name"=>"sales_list",
		"label"=>"Sales List",
		"controller"=>"sales",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_sales',
				'label'=>'Detail Sales',
				'controller'=>'sales',
				'method'=>'detail',
			),
			array(
				'name'=>'delete_sales',
				'label'=>'Delete',
				'controller'=>'sales',
				'method'=>'delete',
			),
			array(
				'name'=>'detail_sales_return',
				'label'=>'Detail Return',
				"requirements"=>array(
					"sales_return"=>true,
				),
			),
			array(
				'name'=>'delete_sales_return',
				'label'=>'Delete Sales Return',
				"requirements"=>array(
					"sales_return"=>true,
				),
			),
			array(
				'name'=>'add_sales_return',
				'label'=>'Add Return',
				'controller'=>'retail',
				'method'=>'sales_return',
				"requirements"=>array(
					"sales_return"=>true,
				),
			),
			array(
				'name'=>'detail_sales_revision',
				'label'=>'Detail Revision',
				"requirements"=>array(
					"have_sales_revision"=>true,
				),
			),
			array(
				'name'=>'add_sales_revision',
				'label'=>'Add Sales Revision',
				'controller'=>'sales_revision',
				'method'=>'add',
				"requirements"=>array(
					"have_sales_revision"=>true,
				),
			),
			array(
				'name'=>'print_sales_bill',
				'label'=>'Print Bill',
				"requirements"=>array(
					"have_sales_bill"=>true,
				),
			),
			array(
				'name'=>'print_sales_waybill',
				'label'=>'Print Way Bill',
				"requirements"=>array(
					"have_sales_waybill"=>true,
				),
			),
			array(
				'name'=>'change_title',
				'label'=>'Change Title',
				'controller'=>'sales',
				'method'=>'change_title',
				"requirements"=>array(
					"sales_change_title"=>true,
				),
			),
			array(
				'name'=>'custom_sales_waybill',
				'label'=>'Custom Way Bill',
				"requirements"=>array(
					"have_custom_sales_waybill"=>true,
				),
			),
			array(
				'name'=>'delete_custom_sales_waybill',
				'label'=>'Delete Custom Way Bill',
				'controller'=>'custom_waybill',
				'method'=>'delete',
				"requirements"=>array(
					"have_custom_sales_waybill"=>true,
				),
			),
		),//end action sales_list
	),
	array(
		"name"=>"sales_return_list",
		"label"=>"Sales Return List",
		"controller"=>"sales_return",
		"method"=>"index",
		"requirements"=>array(
			"sales_return"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_sales_return',
				'label'=>'Detail Sales Return',
				'controller'=>'sales_return',
				'method'=>'detail',
			),
			array(
				'name'=>'detail_sales',
				'label'=>'Detail Sales',
			),
			array(
				'name'=>'print_sales_return_bill',
				'label'=>'Print Return Bill',
				"requirements"=>array(
					"have_sales_return_bill"=>true,
				),
			),
			array(
				'name'=>'delete_sales_return',
				'label'=>'Delete Sales Return',
				'controller'=>'sales_return',
				'method'=>'delete',
			),
			
		),//end action sales_return_list 
	),
	array(
		"name"=>"sales_revision_list",
		"label"=>"Sales Revision List",
		"controller"=>"sales_revision",
		"method"=>"index",
		"requirements"=>array(
			"have_sales_revision"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_sales_revision',
				'label'=>'Detail Sales Revision',
				'controller'=>'sales_revision',
				'method'=>'detail',
			),
			array(
				'name'=>'detail_sales',
				'label'=>'Detail Sales',
			),
			array(
				'name'=>'print_sales_revision_bill',
				'label'=>'Print Revision Bill',
			),
			array(
				'name'=>'delete_sales_revision',
				'label'=>'Delete Sales Revision',
				'controller'=>'sales_revision',
				'method'=>'delete',
			),
			
		),//end action sales_return_list 
	),
	array(
		"name"=>"sales_refund",
		"label"=>"Sales Refund",
		"controller"=>"sales_refund",
		"method"=>"add",
		"requirements"=>array(
			"have_sales_refund"=>true,
		),
	),
	array(
		"name"=>"sales_refund_list",
		"label"=>"Sales Refund List",
		"controller"=>"sales_refund",
		"method"=>"index",
		"requirements"=>array(
			"have_sales_refund"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_sales_refund',
				'label'=>'Detail Sales Refund',
				'controller'=>'sales_refund',
				'method'=>'detail',
			),
			array(
				'name'=>'print_sales_refund_bill',
				'label'=>'Print Refund Bill',
			),
			array(
				'name'=>'delete_sales_refund',
				'label'=>'Delete Purchase Refund',
				'controller'=>'sales_refund',
				'method'=>'delete',
			),
			
		),//end action sales_refund_list 
	),
	array(
		"name"=>"sales_receipt",
		"label"=>"Sales Receipt",
		"controller"=>"retail",
		"method"=>"sales_receipt",
		"requirements"=>array(
			"have_sales_receipt"=>true,
		),
	),
	array(
		"name"=>"sales_receipt_list",
		"label"=>"Sales Receipt List",
		"controller"=>"sales_receipt",
		"method"=>"index",
		"requirements"=>array(
			"have_sales_receipt"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_sales_receipt',
				'label'=>'Detail Sales Receipt',
				'controller'=>'sales_receipt',
				'method'=>'detail',
			),
			array(
				'name'=>'detail_sales',
				'label'=>'Detail Sales',
			),
			array(
				'name'=>'print_sales_receipt_bill',
				'label'=>'Print Receipt',
				"requirements"=>array(
					"have_sales_receipt_bill"=>true,
				),
			),
			array(
				'name'=>'delete_sales_receipt',
				'label'=>'Delete Sales Receipt',
				'controller'=>'sales_receipt',
				'method'=>'delete',
			),
			
		),//end action sales_receipt_list 
	),
);