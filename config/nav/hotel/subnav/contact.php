<?php

return array(
	array(
		"name" => "contact_guest",
		"label" => "Guest",
		"subnav" => array(
			array(
				"name" => "contact_guest_group",
				"label" => "Guest Group",
				"controller" => "hotel_guest_group",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'add_hotel_guest_group',
						'label' => 'Add',
						'controller' => 'hotel_guest_group',
						'method' => 'add',
					),
					array(
						'name' => 'edit_hotel_guest_group',
						'label' => 'Edit',
						'controller' => 'hotel_guest_group',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_hotel_guest_group',
						'label' => 'Delete',
						'controller' => 'hotel_guest_group',
						'method' => 'delete',
					),
				),
				"requirements" => array(
					"have_contact_guest_group" => true,
				),
			),
			array(
				"name" => "contact_guest",
				"label" => "Guest",
				"controller" => "hotel_guest",
				"method" => "index",
				'action' => array(
					array(
						'name' => 'detail_hotel_guest',
						'label' => 'Detail',
						'controller' => 'hotel_guest',
						'method' => 'detail',
					),
					array(
						'name' => 'add_hotel_guest',
						'label' => 'Add',
						'controller' => 'hotel_guest',
						'method' => 'add',
					),
					array(
						'name' => 'edit_hotel_guest',
						'label' => 'Edit',
						'controller' => 'hotel_guest',
						'method' => 'edit',
					),
					array(
						'name' => 'delete_hotel_guest',
						'label' => 'Delete',
						'controller' => 'hotel_guest',
						'method' => 'delete',
					),
				),
				"requirements" => array(
					"have_contact_guest" => true,
				),
			),
		),
	),
);

