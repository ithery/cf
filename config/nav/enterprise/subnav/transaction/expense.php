<?php

return array(
	array(
		"name"=>"expense_type",
		"label"=>"Expense Type",
		"controller"=>"expense_type",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'add_expense_type',
				'label'=>'Add',
				'controller'=>'expense_type',
				'method'=>'add',
				"requirements"=>array(
					"have_expense"=>true,
				),
			),
			array(
				'name'=>'edit_expense_type',
				'label'=>'Edit',
				'controller'=>'expense_type',
				'method'=>'edit',
				"requirements"=>array(
					"have_expense"=>true,
				),
			),
			array(
				'name'=>'delete_expense_type',
				'label'=>'Delete',
				'controller'=>'expense_type',
				'method'=>'delete',
				"requirements"=>array(
					"have_expense"=>true,
				),
			),
			
		),
	),
	array(
		"name"=>"expense",
		"label"=>"Expense",
		"controller"=>"expense",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'add_expense',
				'label'=>'Add',
				'controller'=>'expense',
				'method'=>'add',
				"requirements"=>array(
					"have_expense"=>true,
				),
			),
			
			array(
				'name'=>'delete_expense',
				'label'=>'Delete',
				'controller'=>'expense',
				'method'=>'delete',
				"requirements"=>array(
					"have_expense"=>true,
				),
			),
			
		),
	),
	
);