<?php
defined('SYSPATH') OR die('No direct access allowed.');


$app = CApp::instance();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<?php echo $head_client_script; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="<?php echo curl::base(); ?>media/img/favico.png">
<link href="<?php echo curl::base(); ?>media/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/bootstrap-responsive.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/bootstrap-docs.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/font-awesome.css" rel="stylesheet">

<link href="<?php echo curl::base(); ?>media/css/plugins/dialog2/jquery.dialog2.css" rel="stylesheet">

<link href="<?php echo curl::base(); ?>media/css/plugins/chosen/chosen.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/select2/select2.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/multiselect/multi-select.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/uniform/uniform.css" rel="stylesheet">

<!-- timepicker -->
<link href="<?php echo curl::base(); ?>media/css/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<!-- colorpicker -->
<link href="<?php echo curl::base(); ?>media/css/plugins/colorpicker/colorpicker.css" rel="stylesheet">
<!-- Datepicker -->
<link href="<?php echo curl::base(); ?>media/css/plugins/datepicker/datepicker.css" rel="stylesheet">
<!-- Plupload -->


<link href="<?php echo curl::base(); ?>media/css/plugins/image-gallery/bootstrap-image-gallery.min.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/bootstrap-switch/bootstrap-switch.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/notify/bootstrap-notify.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/notify/bootstrap-notify-alert-backgloss.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/vkeyboard/bootstrap-vkeyboard.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/terminal/jquery.terminal.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/plugins/validation-engine/jquery.validationEngine.css" media="screen" rel="stylesheet" >

<link href="<?php echo curl::base(); ?>media/css/plugins/elfinder/elfinder.min.css" media="screen" rel="stylesheet" >

<link href="<?php echo curl::base(); ?>media/css/cresenity.colors.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity.main.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity.responsive.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity.pos.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity.retail.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity.widget.css" rel="stylesheet">

<link href="<?php echo curl::base(); ?>media/css/cresenity.table.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>ccore/css" rel="stylesheet">


<script src="<?php echo curl::base(); ?>media/js/libs/modernizr.custom.js"></script>
</head>
<body>
	<?php echo $custom_header; ?>
	<?php echo $begin_client_script; ?>
	<div class="navbar navbar-fixed-top">

		<div class="navbar-inner">

			<div class="container">

				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>

				<a class="brand" href="<?php echo curl::base(); ?>admin/home">
					<?php
					$web_title = 'CRESENITY ADMINISTRATOR';

					echo $web_title;
					?>			
				</a>		
				<span id="servertime">
				</span>
				<div class="nav-collapse">
					<ul class="nav pull-right">


						<?php if (ccfg::get("multilang")): ?>
							<li class="dropdown">

								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="icon-flag"></i> 
									<?php echo clang::current_lang_name(); ?>
									<b class="caret"></b>
								</a>

								<ul class="dropdown-menu">
									<?php
									$list = clang::get_lang_list();
									foreach ($list as $k => $v) {
										echo '<li><a href="' . curl::base() . 'cresenity/change_lang/' . $k . '">' . $v . '</a></li>';
									}
									?>

								</ul>

							</li>
						<?php endif; ?>
						<li class="dropdown">

							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="icon-user"></i> 
								Cresenity Admin
								<b class="caret"></b>
							</a>

							<ul class="dropdown-menu">
								<!--
								<li><a href="javascript:;">My Profile</a></li>
								<li><a href="javascript:;">My Groups</a></li>
								<li class="divider"></li>
								-->

								<li><a href="<?php echo curl::base(); ?>admin/core/logout">Logout</a></li>
							</ul>

						</li>
					</ul>
					<!--
					<form class="navbar-search pull-right">
							<input type="text" class="search-query" placeholder="Search">
					</form>
					-->
				</div><!--/.nav-collapse -->	

			</div> <!-- /container -->

		</div> <!-- /navbar-inner -->

	</div> <!-- /navbar -->

	<div class="subnavbar" id="subnavbar" >

		<div class="subnavbar-inner">

			<div class="container">
				<?php
				//echo cmenu::populate_menu($role_id);
				echo CNavigation::instance()->render(true);
				?>


			</div> <!-- /container -->

		</div> <!-- /subnavbar-inner -->

	</div> <!-- /subnavbar -->





	<div class="main">

		<div class="main-inner">

			<div id="container" class="container">


				<!--
				<div class="btn-group" style="width: auto; ">
						<a class="btn btn-large tip-bottom" data-original-title="Manage Files"><i class="icon-file"></i></a>
						<a class="btn btn-large tip-bottom" data-original-title="Manage Users"><i class="icon-user"></i></a>
						<a class="btn btn-large tip-bottom" data-original-title="Manage Comments"><i class="icon-comment"></i><span class="label label-important">5</span></a>
						<a class="btn btn-large tip-bottom" data-original-title="Manage Orders"><i class="icon-shopping-cart"></i></a>
				</div>
				-->
				<!--
				<div class="container-fluid">
				-->
				<?php if ($show_title): ?>
					<h1><?php echo $title ?></h1>
				<?php endif; ?>
				<?php if ($show_breadcrumb): ?>
					<?php
					if ($breadcrumb == "")
						$breadcrumb = $title;
					if (!is_array($breadcrumb))
						$breadcrumb = array();
					if (Router::$controller != "home"):
						?>
						<div id="breadcrumb">
							<a href="<?php echo curl::base(); ?>admin/home" class="tip-left" data-original-title="Go to Home"><i class="icon-home"></i> Home</a>
							<?php foreach ($breadcrumb as $k => $b) : ?>
								<a href="<?php echo $b ?>" class=""><?php echo $k; ?></a>
						<?php endforeach; ?>
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="current"><?php echo $title; ?></a>
						</div>
						<?php
					endif;
					?>
				<?php endif; ?>
				<?php
				$msg = cmsg::flash_all();
				if (strlen($msg) > 0) {
					echo '<div class="row-fluid"><div class="span12">' . $msg . '</div></div>';
				}
				?>
				