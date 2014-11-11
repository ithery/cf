<?php

return array (
	array(
		"name"=>"debit_note_list",
		"label"=>"Debit Note List",
		"controller"=>"debit_note",
		"method"=>"index",
		"requirements"=>array(
			"have_debit_note"=>true,
		),
		
		'action'=>array(
			array(
				'name'=>'add_debit_note',
				'label'=>'Add',
				'controller'=>'debit_note',
				'method'=>'add',
				"requirements"=>array(
					"have_create_debit_note"=>true,
				),
			),
			array(
				'name'=>'delete_debit_note',
				'label'=>'Delete',
				'controller'=>'debit_note',
				'method'=>'delete',
				"requirements"=>array(
					"have_create_debit_note"=>true,
				),
			),
			array(
				'name'=>'detail_debit_note',
				'label'=>'Detail',
				'controller'=>'debit_note',
				'method'=>'detail',
			),
			array(
				'name'=>'write_off_debit_note',
				'label'=>'Write Off',
				'controller'=>'debit_note',
				'method'=>'write_off',
			),
			array(
				'name'=>'cashing_debit_note',
				'label'=>'Cashing',
				'controller'=>'debit_note',
				'method'=>'cashing',
			),
			array(
				'name'=>'detail_transaction',
				'label'=>'Detail Transaction',
			),
			
		),//end action 
	),
	array(
		"name"=>"debit_note_cashing_list",
		"label"=>"Debit Note Cashing List",
		"controller"=>"debit_note_cashing",
		"method"=>"index",
		"requirements"=>array(
			"have_debit_note"=>true,
		),
		
		'action'=>array(
			array(
				'name'=>'delete_debit_note_cashing',
				'label'=>'Delete',
				'controller'=>'debit_note_cashing',
				'method'=>'delete',
			),
			array(
				'name'=>'detail_debit_note_cashing',
				'label'=>'Detail',
				'controller'=>'debit_note_cashing',
				'method'=>'detail',
			),
		),//end action 
	),
	array(
		"name"=>"debit_note_write_off_list",
		"label"=>"Debit Note Write Off List",
		"controller"=>"debit_note_write_off",
		"method"=>"index",
		"requirements"=>array(
			"have_debit_note"=>true,
		),
		
		'action'=>array(
			array(
				'name'=>'delete_debit_note_write_off',
				'label'=>'Delete',
				'controller'=>'debit_note_write_off',
				'method'=>'delete',
			),
			array(
				'name'=>'detail_debit_note_write_off',
				'label'=>'Detail',
				'controller'=>'debit_note_write_off',
				'method'=>'detail',
			),
		),//end action 
	),
);