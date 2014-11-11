<?php

return array(
	array(
		"name" => "access",
		"label" => "Access",
		"subnav" => array(
			array(
				"name" => "roles",
				"label" => "Roles",
				"controller" => "roles",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_roles',
						'label' => 'Add',
						'controller' => 'roles',
						'method' => 'add',
					),
					array(
						'name' => 'edit_roles',
						'label' => 'Edit',
						'controller' => 'roles',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_roles',
						'label' => 'Delete',
						'controller' => 'roles',
						'method' => 'delete',
					),
				), //end action roles
			),
			array(
				"name" => "users",
				"label" => "Users",
				"controller" => "users",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_users',
						'label' => 'Add',
						'controller' => 'users',
						'method' => 'add',
					),
					array(
						'name' => 'edit_users',
						'label' => 'Edit',
						'controller' => 'users',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_users',
						'label' => 'Delete',
						'controller' => 'users',
						'method' => 'delete',
					),
				), //end action users
			),
			array(
				"name" => "user_permission",
				"label" => "Users Permission",
				"controller" => "user_permission",
				"method" => "index",
			),
		), //end subnav access
	),
	array(
		"name" => "hotel_payment_type_menu",
		"label" => "Payment Type",
		"subnav" => array(
			array(
				"name" => "hotel_payment_type",
				"label" => "Payment Type",
				"controller" => "hotel_payment_type",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_hotel_payment_type',
						'label' => 'Add',
						'controller' => 'hotel_payment_type',
						'method' => 'add',
					),
					array(
						'name' => 'edit_hotel_payment_type',
						'label' => 'Edit',
						'controller' => 'hotel_payment_type',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_hotel_payment_type',
						'label' => 'Delete',
						'controller' => 'hotel_payment_type',
						'method' => 'delete',
					),
				), //end action bank
			),
			array(
				"name" => "hotel_payment_card",
				"label" => "Payment Card",
				"controller" => "hotel_payment_card",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_hotel_payment_card',
						'label' => 'Add',
						'controller' => 'hotel_payment_card',
						'method' => 'add',
					),
					array(
						'name' => 'edit_hotel_payment_card',
						'label' => 'Edit',
						'controller' => 'hotel_payment_card',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_hotel_payment_card',
						'label' => 'Delete',
						'controller' => 'hotel_payment_card',
						'method' => 'delete',
					),
				), //end action bank
			),
		),
	),
	//hotel
	array(
		"name" => "hotel_front_office_data",
		"label" => "Front Office Data",
		"subnav" => array(
			array(
				"name" => "hotel_food",
				"label" => "F & B",
				"controller" => "hotel_food",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_hotel_food',
						'label' => 'Add',
						'controller' => 'hotel_food',
						'method' => 'add',
					),
					array(
						'name' => 'edit_hotel_food',
						'label' => 'Edit',
						'controller' => 'hotel_food',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_hotel_food',
						'label' => 'Delete',
						'controller' => 'hotel_food',
						'method' => 'delete',
					),
				), 
			),
			array(
				"name" => "hotel_laundry",
				"label" => "Laundry",
				"controller" => "hotel_laundry",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_hotel_laundry',
						'label' => 'Add',
						'controller' => 'hotel_laundry',
						'method' => 'add',
					),
					array(
						'name' => 'edit_hotel_laundry',
						'label' => 'Edit',
						'controller' => 'hotel_laundry',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_hotel_laundry',
						'label' => 'Delete',
						'controller' => 'hotel_laundry',
						'method' => 'delete',
					),
				), 
			),
			array(
				"name" => "hotel_charge",
				"label" => "Charge",
				"controller" => "hotel_charge",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_hotel_charge',
						'label' => 'Add',
						'controller' => 'hotel_charge',
						'method' => 'add',
					),
					array(
						'name' => 'edit_hotel_charge',
						'label' => 'Edit',
						'controller' => 'hotel_charge',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_hotel_charge',
						'label' => 'Delete',
						'controller' => 'hotel_charge',
						'method' => 'delete',
					),
				),
			),
		), //end subnav access
	),
);