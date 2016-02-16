<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php 
    if (!isset($cheader)) {
        $cheader = 'cheader';
    }
    if (!isset($cfooter)) {
        $cfooter = 'cfooter';
    }if (!isset($cmenu)) {
        $cmenu = 'cmenu';
    }
    $path = '';
    if (isset($theme_path)) {
        $path = $theme_path .'/';
    }
        
	$h = CView::factory($path .$cheader);
        $h->theme = $theme;
	$h->show_breadcrumb = $show_breadcrumb;
	$h->show_title = $show_title;
	$h->breadcrumb = $breadcrumb;
	$h->title = $title;
	$h->custom_header = $custom_header;
	$h->head_client_script = $head_client_script;
	$h->begin_client_script = $begin_client_script;
	$h->css_hash = $css_hash;
	$h->js_hash = $css_hash;

	$m = CView::factory($path .$cmenu);
    $m->theme = $theme;
	$m->show_breadcrumb = $show_breadcrumb;
	$m->show_title = $show_title;
	$m->breadcrumb = $breadcrumb;
	$m->title = $title;
	$m->custom_header = $custom_header;
	$m->head_client_script = $head_client_script;
	$m->begin_client_script = $begin_client_script;
	$m->css_hash = $css_hash;
	$m->js_hash = $css_hash;
	
	$f = CView::factory($path .$cfooter);
        $f->theme = $theme;
	$f->js = $js;
	$f->custom_js = $custom_js;
	$f->custom_footer = $custom_footer;
	$f->end_client_script = $end_client_script;
	$f->load_client_script = $load_client_script;
	$f->ready_client_script = $ready_client_script;
	$f->css_hash = $css_hash;
	$f->js_hash = $js_hash;
	echo $h->render();
	echo '<div id="wrapper">';
	echo $m->render();
	echo '<div id="page-content-wrapper">
		<div class="overlay"></div>
		<button type="button" id="hamburgerfloat" class="hamburger is-closed" data-toggle="offcanvas" style="display: none;">
            <span class="hamb-top"></span>
			<span class="hamb-middle"></span>
			<span class="hamb-bottom"></span>
        </button>';
	echo $content;
	echo $f->render();
	echo '</div>';
	echo '</div>';
?>