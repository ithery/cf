<?php

return array(
		array(
			"name"=>"cost",
			"label"=>"Cost",
			"controller"=>"cost",
			"method"=>"index",
			'action'=>array(
				array(
					'name'=>'add_cost',
					'label'=>'Add',
					'controller'=>'cost',
					'method'=>'add',
				),
				array(
					'name'=>'edit_cost',
					'label'=>'Edit',
					'controller'=>'cost',
					'method'=>'edit',
				),
				array(
					'name'=>'delete_cost',
					'label'=>'Delete',
					'controller'=>'cost',
					'method'=>'delete',
				),
			),
			"requirements"=>array(
				"have_cost"=>true,
			),
			//end action cost
		),
		array(
			"name"=>"cost_menu_list",
			"label"=>"Global Cost",
			"requirements"=>array(
				"have_global_cost"=>true,
			),
			"subnav"=>array(
				array(
					"name"=>"global_cost",
					"label"=>"Global Cost",
					"controller"=>"cost_global",
					"method"=>"add",
				),
				array(
					"name"=>"detail_global_cost",
					"label"=>"Detail Global Cost",
					"controller"=>"cost_global",
					"method"=>"index",
					'action'=>array(
						array(
							'name'=>'add_global_cost',
							'label'=>'Add',
							'controller'=>'cost_global',
							'method'=>'add',
						),
						// array(
							// 'name'=>'edit_cost',
							// 'label'=>'Edit',
							// 'controller'=>'cost_global',
							// 'method'=>'edit',
						// ),
						array(
							'name'=>'delete_cost',
							'label'=>'Delete',
							'controller'=>'cost_global',
							'method'=>'delete',
						),
					),
					"requirements"=>array(
						"have_global_cost"=>true,
					),
					//end action cost
				),
			),
		),
	);
	