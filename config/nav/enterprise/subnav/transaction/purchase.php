<?php
return array(
	array(
		"name"=>"purchase",
		"label"=>"Purchase",
		"controller"=>"purchase",
		"method"=>"add",
	),
	
	array(
		"name"=>"purchase_list",
		"label"=>"Purchase List",
		"controller"=>"purchase",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_purchase',
				'label'=>'Detail Purchase',
				'controller'=>'purchase',
				'method'=>'detail',
			),
			array(
				'name'=>'delete_purchase',
				'label'=>'Delete',
				'controller'=>'purchase',
				'method'=>'delete',
			),
			array(
				'name'=>'detail_purchase_return',
				'label'=>'Detail Return',
				"requirements"=>array(
					"purchase_return"=>true,
				),
			),
			array(
				'name'=>'delete_purchase_return',
				'label'=>'Delete Purchase Return',
				"requirements"=>array(
					"purchase_return"=>true,
				),
			),
			array(
				'name'=>'add_purchase_return',
				'label'=>'Add Return',
				'controller'=>'retail',
				'method'=>'purchase_return',
				"requirements"=>array(
					"purchase_return"=>true,
				),
			),
			array(
				'name'=>'detail_purchase_revision',
				'label'=>'Detail Revision',
				"requirements"=>array(
					"have_purchase_revision"=>true,
				),
			),
			array(
				'name'=>'detail_purchase_payment',
				'label'=>'Detail Purchase Payment',
				"requirements"=>array(
					"have_purchase_credit"=>true,
				),
			),
			array(
				'name'=>'add_purchase_revision',
				'label'=>'Add Purchase Revision',
				'controller'=>'purchase_revision',
				'method'=>'add',
				"requirements"=>array(
					"have_purchase_revision"=>true,
				),
			),
		),//end action purchase_list 
	),
	
	array(
		"name"=>"purchase_return_list",
		"label"=>"Purchase Return List",
		"controller"=>"purchase_return",
		"method"=>"index",
		"requirements"=>array(
			"purchase_return"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_purchase_return',
				'label'=>'Detail Purchase Return',
				'controller'=>'purchase_return',
				'method'=>'detail',
			),
			array(
				'name'=>'detail_purchase',
				'label'=>'Detail Purchase',
			),
			array(
				'name'=>'print_purchase_return_bill',
				'label'=>'Print Return Bill',
				"requirements"=>array(
					"have_purchase_return_bill"=>true,
				),
			),
			array(
				'name'=>'delete_purchase_return',
				'label'=>'Delete Purchase Return',
				'controller'=>'purchase_return',
				'method'=>'delete',
			),
			
		),//end action purchase_return_list 
	),
	array(
		"name"=>"purchase_revision_list",
		"label"=>"Purchase Revision List",
		"controller"=>"purchase_revision",
		"method"=>"index",
		"requirements"=>array(
			"have_purchase_revision"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_purchase_revision',
				'label'=>'Detail Purchase Revision',
				'controller'=>'purchase_revision',
				'method'=>'detail',
			),
			array(
				'name'=>'detail_purchase',
				'label'=>'Detail Purchase',
			),
			array(
				'name'=>'print_purchase_revision_bill',
				'label'=>'Print Revision Bill',
			),
			array(
				'name'=>'delete_purchase_revision',
				'label'=>'Delete Purchase Revision',
				'controller'=>'purchase_revision',
				'method'=>'delete',
			),
			
		),//end action purchase_return_list 
	),
	
	array(
		"name"=>"purchase_refund",
		"label"=>"Purchase Refund",
		"controller"=>"purchase_refund",
		"method"=>"add",
		"requirements"=>array(
			"have_purchase_refund"=>true,
		),
	),
	array(
		"name"=>"purchase_refund_list",
		"label"=>"Purchase Refund List",
		"controller"=>"purchase_refund",
		"method"=>"index",
		"requirements"=>array(
			"have_purchase_refund"=>true,
		),
		'action'=>array(
			array(
				'name'=>'detail_purchase_refund',
				'label'=>'Detail Purchase Refund',
				'controller'=>'purchase_refund',
				'method'=>'detail',
			),
			array(
				'name'=>'print_purchase_refund_bill',
				'label'=>'Print Refund Bill',
			),
			array(
				'name'=>'delete_purchase_refund',
				'label'=>'Delete Purchase Refund',
				'controller'=>'purchase_refund',
				'method'=>'delete',
			),
			
		),//end action purchase_refund_list 
	),
);