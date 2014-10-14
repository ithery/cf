<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php 
	$h = CView::factory('admin/cheader');
	$h->show_breadcrumb = $show_breadcrumb;
	$h->show_title = $show_title;
	$h->breadcrumb = $breadcrumb;
	$h->title = $title;
	$h->custom_header = $custom_header;
	$h->head_client_script = $head_client_script;
	$h->begin_client_script = $begin_client_script;
	
	$f = CView::factory('admin/cfooter');
	$f->js = $js;
	$f->custom_js = $custom_js;
	$f->custom_footer = $custom_footer;
	$f->end_client_script = $end_client_script;
	$f->load_client_script = $load_client_script;
	$f->ready_client_script = $ready_client_script;
	echo $h->render();
	echo $content;
	echo $f->render();
?>