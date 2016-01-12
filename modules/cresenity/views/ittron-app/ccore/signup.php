<?php defined('SYSPATH') OR die('No direct access allowed.'); 

	$config = CConfig::factory()->get_array();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sign Up</title>
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
				<?php echo CF::config("cresenity.title"); ?>					
			</a>		
			
			<div class="nav-collapse">
				<ul class="nav pull-right">
					<?php if(ccfg::get("multilang")): ?>
					<li class="dropdown">
						
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-flag"></i> 
							<?php echo clang::current_lang_name(); ?>
							<b class="caret"></b>
						</a>
						
						<ul class="dropdown-menu">
							<?php
								$list = clang::get_lang_list();
								foreach($list as $k=>$v) {
									echo '<li><a href="'.curl::base().'cresenity/change_lang/'.$k.'">'.$v.'</a></li>';
								}
							?>
						
						</ul>
						
					</li>
					<?php endif; ?>
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
		
			<h1>Create Your Account</h1>			
			
			
			<div class="login-fields">
				
				<p>Create your free account:</p>
				<div class="field">
					<label for="org_name">Company:</label>
					<input type="text" id="org_name" name="org_name" value="" placeholder="Company" class="login" autocomplete="off"/>
				</div> <!-- /field -->
				
				
				<div class="field">
					<label for="email">Email Address:</label>
					<input type="text" id="email" name="email" value="" placeholder="Email" class="login" autocomplete="off"/>
				</div> <!-- /field -->
				
				<div class="field">
					<label for="password">Password:</label>
					<input type="password" id="password" name="password" value="" placeholder="Password" class="login" autocomplete="off"/>
				</div> <!-- /field -->
				
				<div class="field">
					<label for="confirm_password">Confirm Password:</label>
					<input type="password" id="confirm_password" name="confirm_password" value="" placeholder="Confirm Password" class="login" autocomplete="off"/>
				</div> <!-- /field -->
				
			</div> <!-- /login-fields -->
			
			<div class="login-actions">
				<!--
				<span class="login-checkbox">
					<input id="Field" name="agree" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
					<label class="choice" for="agree">I have read and agree with the Terms of Use.</label>
				</span>
				-->				
				<button class="button btn btn-primary btn-large">Register</button>
				
			</div> <!-- .actions -->
			
		</form>
		
	</div> <!-- /content -->
	
</div> <!-- /account-container -->


<!-- Text Under Box -->
<div class="login-extra">
	Already have an account? <a href="<?php echo curl::base(); ?>">Login</a>
</div> <!-- /login-extra -->


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
			/*
			 * JS SignUp effect
			 * This script will enable effects for the signup page
			 */
				// Elements
			var doc = jQuery('html').addClass('js-signup'),
				container = jQuery('#container'),
				formSignUp = jQuery('#form-signup'),

				// If layout is centered
				centered;

			/******* EDIT THIS SECTION *******/

			/*
			 * AJAX login
			 * This function will handle the login process through AJAX
			 */
			formSignUp.submit(function(event)
			{
				event.preventDefault();

				// Values
				var login = jQuery.trim(jQuery('#email').val()),
					pass = jQuery.trim(jQuery('#password').val()),
					pass2 = jQuery.trim(jQuery('#confirm_password').val());

				// Check inputs
				if (login.length === 0) {
					// Remove previous messages
					formSignUp.clearMessages();
					// Display message
					displayError('Please fill in your email');
					return false;
				}
				else if (pass.length === 0) {
					// Remove previous messages
					formSignUp.clearMessages();
					// Display message
					displayError('Please fill in your password');
					return false;
				}
				else if (pass2 != pass) {
					// Remove previous messages
					formSignUp.clearMessages();
					// Display message
					displayError('Password doesn\'t match');
					return false;
				}
				
				else {
					// Remove previous messages
					formSignUp.clearMessages();

					// Show progress
					displayLoading('Submitting your data...');
					event.preventDefault();

					// Stop normal behavior
					event.preventDefault();

					/*
					 * This is where you may do your AJAX call, for instance:
					 */
					var url = '<?php echo curl::base(); ?>index.php/cresenity/signup';
					
					jQuery.ajax(url, {
						dataType: 'json',
						type: 'POST',
						data : $("#form-signup").serialize(),
						success: function(data) {
							var result = data.result;
							var message = data.message;
							if(result=="OK") {
								formSignUp.clearMessages();
								displaySuccess("success register, please login with your account");
								setTimeout(function() {
									window.location.href="<?php echo curl::base(); ?>";
								},1000);
							} else {
								formSignUp.clearMessages();
								displayError(message);
							}
							
						},
						error: function() {
							formSignUp.clearMessages();
							displayError('Error while contacting server, please try again');
						}
					});
					

					// Simulate server-side check
					//setTimeout(function() {
					//	document.location.href = './'
					//}, 2000);
				}
			});

			/******* END OF EDIT SECTION *******/

			// Handle resizing (mostly for debugging)
			function handleLoginResize()
			{
				// Detect mode
				centered = (container.css('position') === 'absolute');

				// Set min-height for mobile layout
				if (!centered)
				{
					container.css('margin-top', '');
				}
				else
				{
					if (parseInt(container.css('margin-top'), 10) === 0)
					{
						centerForm(false);
					}
				}
			};

			// Register and first call
			$(window).bind('normalized-resize', handleLoginResize);
			handleLoginResize();

			/*
			 * Center function
			 * @param boolean animate whether or not to animate the position change
			 * @param string|element|array any jQuery selector, DOM element or set of DOM elements which should be ignored
			 * @return void
			 */
			function centerForm(animate, ignore)
			{
				// If layout is centered
				if (centered)
				{
					var siblings = formSignUp.siblings(),
						finalSize = formSignUp.outerHeight();

					// Ignored elements
					if (ignore)
					{
						siblings = siblings.not(ignore);
					}

					// Get other elements height
					siblings.each(function(i)
					{
						finalSize += $(this).outerHeight(true);
					});

					// Setup
					container[animate ? 'animate' : 'css']({ marginTop: -Math.round(finalSize/2)+'px' });
				}
			};

			// Initial vertical adjust
			centerForm(false);

			/**
			 * Function to display error messages
			 * @param string message the error to display
			 */
			function displayError(message)
			{
				// Show message
				var message = formSignUp.message(message, {
					append: false,
					arrow: 'none',
					classes: ['red-gradient'],
					animate: false					// We'll do animation later, we need to know the message height first
				});

				// Vertical centering (where we need the message height)
				centerForm(true, 'fast');

				// Watch for closing and show with effect
				message.bind('endfade', function(event)
				{
					// This will be called once the message has faded away and is removed
					centerForm(true, message.get(0));

				}).hide().slideDown('fast');
			}
			
			/**
			 * Function to display error messages
			 * @param string message the error to display
			 */
			function displaySuccess(message)
			{
				// Show message
				var message = formSignUp.message(message, {
					append: false,
					arrow: 'none',
					classes: ['green-gradient'],
					animate: false					// We'll do animation later, we need to know the message height first
				});

				// Vertical centering (where we need the message height)
				centerForm(true, 'fast');

				// Watch for closing and show with effect
				message.bind('endfade', function(event)
				{
					// This will be called once the message has faded away and is removed
					centerForm(true, message.get(0));

				}).hide().slideDown('fast');
			}

			/**
			 * Function to display loading messages
			 * @param string message the message to display
			 */
			function displayLoading(message)
			{
				// Show message
				var message = formSignUp.message('<strong>'+message+'</strong>', {
					append: false,
					arrow: 'none',
					classes: ['blue-gradient', 'align-center'],
					stripes: true,
					darkStripes: false,
					closable: false,
					animate: false					// We'll do animation later, we need to know the message height first
				});

				// Vertical centering (where we need the message height)
				centerForm(true, 'fast');

				// Watch for closing and show with effect
				message.bind('endfade', function(event)
				{
					// This will be called once the message has faded away and is removed
					centerForm(true, message.get(0));

				}).hide().slideDown('fast');
			}
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