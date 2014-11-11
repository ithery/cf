<?php
include dirname(__FILE__)."/subconfig/contact.php";
include dirname(__FILE__)."/subconfig/room.php";
include dirname(__FILE__)."/subconfig/global.php";

$config=array(
	array(
		"name"=>"global_setting",
		"label"=>"Global",
		"config"=>$global,
	),
	array(
		"name"=>"contact_setting",
		"label"=>"Contact",
		"config"=>$contact,
	),
	array(
		"name"=>"room_setting",
		"label"=>"Room",
		"config"=>$room,
	),
);


unset($contatc);
unset($room);

