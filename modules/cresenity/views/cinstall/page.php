<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php 
	$h = View::factory('install/header');
	$h->title = $title;
	
	echo $h->render();
	echo $content;
	$f = View::factory('install/footer');
	$f->js=$js;
	echo $f->render();
?>