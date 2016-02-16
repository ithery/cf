<?php defined('SYSPATH') OR die('No direct access allowed.'); 

	$config = CConfig::factory()->get_array();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?php echo curl::base(); ?>media/css/cresenity.reset.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/bootstrap-responsive.css" rel="stylesheet">

<link href="<?php echo curl::base(); ?>media/css/font-awesome.css" rel="stylesheet">

<link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.colors.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.form.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.message.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.utilities.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.generic-element.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.block-arrow.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity.main.css" rel="stylesheet">
<link href="<?php echo curl::base(); ?>media/css/cresenity.login.css" rel="stylesheet">
<!-- JavaScript at bottom except for Modernizr -->
<script src="<?php echo curl::base(); ?>media/js/libs/modernizr.custom.js"></script>

</head>
<body>
	
<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<a class="brand" href="./">
				Cresenity APP				
			</a>		
			
			<div class="nav-collapse">
				<ul class="nav pull-right">
					
					<li class="">						
						<a href="<?php echo curl::base(); ?>" class="">
							<i class="icon-chevron-left"></i>
							Back to Homepage
						</a>
						
					</li>
				</ul>
				
			</div><!--/.nav-collapse -->	
	
		</div> <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->



<div class="account-container register">
	<div class="logo">
		<img src="<?php echo curl::base(); ?>media/img/cresenity-logo.png" />
	</div>
	
	<div class="content clearfix" id="container">
		
		<form id="form-signup" action="" method="post">
		
		
			
			<div class="login-fields">
				
				<div class="well"><h1>Success resend activation email</h1>
				<p>Please check your inbox...</p>
				
				</div>
				
				
			</div> <!-- /login-fields -->
			
			
			
		</form>
		
	</div> <!-- /content -->
	
</div> <!-- /account-container -->




	<!-- Load javascript here -->
	<script src="<?php echo curl::base();?>media/js/libs/jquery.js"></script>
	<script src="<?php echo curl::base();?>media/js/libs/jquery.ui.custom.js"></script>
	

	<script src="<?php echo curl::base();?>media/js/libs/bootstrap.min.js"></script>

	
	<script src="<?php echo curl::base();?>media/js/cresenity.setup.js"></script>
	<script src="<?php echo curl::base();?>media/js/cresenity.message.js"></script>
	<script>

		/*
		 * How do I hook my login script to this?
		 * --------------------------------------
		 *
		 * This script is meant to be non-obtrusive: if the user has disabled javascript or if an error occurs, the login form
		 * works fine without ajax.
		 *
		 * The only part you need to edit is the login script between the EDIT SECTION tags, which does inputs validation
		 * and send data to server. For instance, you may keep the validation and add an AJAX call to the server with the
		 * credentials, then redirect to the dashboard or display an error depending on server return.
		 *
		 * Or if you don't trust AJAX calls, just remove the event.preventDefault() part and let the form be submitted.
		 */

		jQuery(document).ready(function()
		{
			setTimeout(function() {
				window.location.href="<?php echo curl::base(); ?>";
			},2000);
		});

		// What about a notification?
		//notify('This is new demo of solusigps', 'Want to see another login page style? Try the <a href="login-box.html"><b>box version</b></a> or the <a href="login-full.html"><b>full version</b></a>.', {
		//	autoClose: false,
		//	delay: 2500,
		//	icon: 'img/demo/icon.png'
		//});

	</script>
</body>
</html>