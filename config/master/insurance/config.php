<?php
include dirname(__FILE__).DIRECTORY_SEPARATOR."subconfig".DIRECTORY_SEPARATOR."global.php";
include dirname(__FILE__).DIRECTORY_SEPARATOR."subconfig".DIRECTORY_SEPARATOR."configuration.php";

$config=array(
	array(
		"name"=>"global_setting",
		"label"=>"Global",
		"config"=>$global,
	),
	
	array(
		"name"=>"configuration_setting",
		"label"=>"Configuration",
		"config"=>$configuration,
	),
	
	
);


unset($global);

unset($configuration);


