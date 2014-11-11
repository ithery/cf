<?php

return array (
	array(
		"name"=>"credit_note_list",
		"label"=>"Credit Note List",
		"controller"=>"credit_note",
		"method"=>"index",
		"requirements"=>array(
			"have_credit_note"=>true,
		),
		
		'action'=>array(
			array(
				'name'=>'add_credit_note',
				'label'=>'Add',
				'controller'=>'credit_note',
				'method'=>'add',
				"requirements"=>array(
					"have_create_credit_note"=>true,
				),
			),
			array(
				'name'=>'delete_credit_note',
				'label'=>'Delete',
				'controller'=>'credit_note',
				'method'=>'delete',
				"requirements"=>array(
					"have_create_credit_note"=>true,
				),
			),
			array(
				'name'=>'detail_credit_note',
				'label'=>'Detail',
				'controller'=>'credit_note',
				'method'=>'detail',
			),
			array(
				'name'=>'write_off_credit_note',
				'label'=>'Write Off',
				'controller'=>'credit_note',
				'method'=>'write_off',
			),
			array(
				'name'=>'cashing_credit_note',
				'label'=>'Cashing',
				'controller'=>'credit_note',
				'method'=>'cashing',
			),
			array(
				'name'=>'detail_transaction',
				'label'=>'Detail Transaction',
			),
			
		),//end action 
	),
	array(
		"name"=>"credit_note_cashing_list",
		"label"=>"Credit Note Cashing List",
		"controller"=>"credit_note_cashing",
		"method"=>"index",
		"requirements"=>array(
			"have_credit_note"=>true,
		),
		
		'action'=>array(
			array(
				'name'=>'delete_credit_note_cashing',
				'label'=>'Delete',
				'controller'=>'credit_note_cashing',
				'method'=>'delete',
			),
			array(
				'name'=>'detail_credit_note_cashing',
				'label'=>'Detail',
				'controller'=>'credit_note_cashing',
				'method'=>'detail',
			),
		),//end action 
	),
	array(
		"name"=>"credit_note_write_off_list",
		"label"=>"Credit Note Write Off List",
		"controller"=>"credit_note_write_off",
		"method"=>"index",
		"requirements"=>array(
			"have_credit_note"=>true,
		),
		
		'action'=>array(
			array(
				'name'=>'delete_credit_note_write_off',
				'label'=>'Delete',
				'controller'=>'credit_note_write_off',
				'method'=>'delete',
			),
			array(
				'name'=>'detail_credit_note_write_off',
				'label'=>'Detail',
				'controller'=>'credit_note_write_off',
				'method'=>'detail',
			),
		),//end action 
	),
);