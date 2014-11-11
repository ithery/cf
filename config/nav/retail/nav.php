<?php

return array(
	array(
		"name"=>"dashboard",
		"label"=>"Dashboard",
		"controller"=>"home",
		"method"=>"index",
		"icon"=>"home",
	),
	array(
		"name"=>"setting_menu",
		"label"=>"Setting",
		"icon"=>"cog",
		"subnav"=>include dirname(__FILE__)."/subnav/setting.php",
	),
);
