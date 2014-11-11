<?php
return array(
	array(
		"name"=>"receiving",
		"label"=>"Receiving",
		"controller"=>"retail",
		"method"=>"receiving",
	),
	
	array(
		"name"=>"receiving_list",
		"label"=>"Receiving List",
		"controller"=>"receiving",
		"method"=>"index",
		'action'=>array(
			array(
				'name'=>'detail_receiving',
				'label'=>'Detail Receiving',
				'controller'=>'receiving',
				'method'=>'detail',
			),
		),//end action receiving_list 
	),
);