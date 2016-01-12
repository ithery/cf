<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php 
        if (!isset($cheader)) 
            $cheader = 'cheader';
        if (!isset($cfooter)) 
            $cfooter = 'cfooter';
    
	$h = CView::factory($cheader);
	$h->show_breadcrumb = $show_breadcrumb;
	$h->show_title = $show_title;
	$h->breadcrumb = $breadcrumb;
	$h->title = $title;
	$h->additional_head = $additional_head;
	$h->custom_header = $custom_header;
	$h->head_client_script = $head_client_script;
	$h->begin_client_script = $begin_client_script;
	$h->css_hash = $css_hash;
	$h->js_hash = $js_hash;
	
	$f = CView::factory($cfooter);
	$f->js = $js;
	$f->custom_js = $custom_js;
	$f->custom_footer = $custom_footer;
	$f->end_client_script = $end_client_script;
	$f->load_client_script = $load_client_script;
	$f->ready_client_script = $ready_client_script;
	$f->css_hash = $css_hash;
	$f->js_hash = $js_hash;
	echo $h->render();
	echo $content;
	echo $f->render();
?>