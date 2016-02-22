<?php
defined('SYSPATH') OR die('No direct access allowed.');


$session = Session::instance();
$user = $session->get("user");
$role_id = "";
$username = "";
$user_id = "";
if ($user) {
    $role_id = $user->role_id;
    $user_id = $user->user_id;
    $username = $user->username;
}
$app = CApp::instance();
$org = $app->org();
?>
<!DOCTYPE html>
<html class="no-js" lang="<?php echo clang::getlang(); ?>" >
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <?php echo $head_client_script; ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="<?php echo curl::base(); ?>media/img/favico.png">

        <link href="<?php echo curl::base(); ?>ccore/css/<?php echo $css_hash; ?>" rel="stylesheet">
		<?php //echo $additional_head;?>
    </head>
    <body>
        <?php echo $custom_header; ?>
        <?php echo $begin_client_script; ?>
		<nav class="nav-header" role="navigation">
			<div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">62Hall</a>
				<ul class="right ">
					<li><a href="#"><i class="material-icons">shopping_cart</i></a></li>
				</ul>

				<div id="nav-mobile" class="side-nav fixed">
				<ul>
					<li><a href="/demo/typography">Typography</a></li>
					<li><a href="/demo/action">Action</a></li>
					<li><a href="/demo/form">Form</a></li>
					<li><a href="/demo/media">Media</a></li>
					<li><a href="/demo2/card">Card</a></li>
				</ul>
				</div>
				<a href="javascript:;" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
			</div>
		</nav>
		<div class="content">
