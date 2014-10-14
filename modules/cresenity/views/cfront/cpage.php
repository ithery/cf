<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php 
	$h = CView::factory('cfront/cheader');
	$h->show_breadcrumb = $show_breadcrumb;
	$h->show_title = $show_title;
	$h->breadcrumb = $breadcrumb;
	$h->title = $title;
	$h->custom_header = $custom_header;
	
	$f = CView::factory('cfront/cfooter');
	$f->js = $js;
	$f->custom_js = $custom_js;
	$f->custom_footer = $custom_footer;
	echo $h->render();
	echo $content;
	echo $f->render();
?>