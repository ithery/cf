<?php
return array(
	array(
		"name"=>"purchase_order",
		"label"=>"Purchase Order",
		"controller"=>"retail",
		"method"=>"purchase_order",
	),
	array(
		"name"=>"purchase_order_list",
		"label"=>"Purchase Order List",
		"controller"=>"purchase_order",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_purchase_order',
				'label'=>'Detail Purchase Order',
				'controller'=>'purchase_order',
				'method'=>'detail',
			),
			array(
				'name'=>'edit_purchase_order',
				'label'=>'Edit',
				'controller'=>'purchase_order',
				'method'=>'edit',
			),
			array(
				'name'=>'delete_purchase_order',
				'label'=>'Delete',
				'controller'=>'purchase_order',
				'method'=>'delete',
			),
			array(
				'name'=>'add_purchase',
				'label'=>'Add Purchase',
			),
				array(
				'name'=>'print_purchase_order',
				'label'=>'Print Purchase Order',
			),
		),//end action purchase_order_list
	),
);