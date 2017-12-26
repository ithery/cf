<?php defined('SYSPATH') OR die('No direct access allowed.');




$config["app_id"] = 1;
$config["install"] = false;
$config["title"] = "CRESENITY";
$config["sidebar"] = true;
$config["signup"] = false;
$config["theme"] = "";
$config["lang"]="id";
$config["admin_email"] = "contact@cresenitytech.com";
$config["set_timezone"] = true;
$config["default_timezone"] = 'Asia/Jakarta';
$config["multilang"]=true;
$config["top_menu_cashier"]=false;
$config["update_last_request"]=true;

$config["ip_address"] = "192.168.1.19";
$config["code_test"] = false;


$config["require_js"] = true;
$config["merge_js"] = false;
$config["minify_js"] = false;

$config["merge_css"] = false;
$config["minify_css"] = false;

return $config;
