<?php
    defined('SYSPATH') OR die('No direct access allowed.');

    $username = "";
    $password = "";
    if (isset($_GET["demo"])) {
        $username = "demo";
        $password = "demo";
    }
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--<link href="<?php echo curl::base(); ?>media/css/cresenity.reset.css" rel="stylesheet">-->
        <link href="<?php echo curl::base(); ?>media/css/plugins/bootstrap-3.3.5/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/plugins/font-awesome/font-awesome 4.5.0.min.css" rel="stylesheet">

        <link href="<?php echo curl::base(); ?>media/css/iapp.min.css" rel="stylesheet">
        <!-- JavaScript at bottom except for Modernizr -->
        <script src="<?php echo curl::base(); ?>media/js/libs/modernizr.custom.js"></script>
        <style>
            .loading {
                padding: 7px !important;
                margin-top: -13px;
            }
        </style>
    </head>
    <body class="hold-transition login-page">

        <div class="login-box">
            <div class="login-logo">
                <?php 
                    $app_code = CF::app_code();
                    $org_code = CF::org_code();
                    if (strlen($org_code) == 0) {
                        $org_code = 'default';
                    }
                    $large_logo = '<img src="' .curl::base() .'media/img/logo-ittron.png" height="80px" />';
                    $is_image = false;
                    $cf_large_logo = curl::base() .'application/' .$app_code .'/' .$org_code .'/media/img/logo.png';
                    $cf_large_logo_path = 'application/'.$app_code .'/' .$org_code .'/media/img/logo.png';
                    if (!file_exists($cf_large_logo_path)) {
                        $cf_large_logo_path = 'media/img/logo.png';
                        if (file_exists($cf_large_logo_path)) {
                            $cf_large_logo = curl::base() .'media/img/logo.png';
                            $is_image = true;
                        }
                    }
                    else {
                        $is_image = true;
                    }
                    if ($is_image) {
                        $large_logo = '<img src="' .$cf_large_logo .'"  height="80px" />';
                    }
                    echo $large_logo;
                ?>
                <br/>
            </div><!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg"><a href="<?php echo curl::base(); ?>"><b><?php echo ccfg::get('title'); ?></b></a></p>
                <form action="" method="post" id="form-login">
                    <div class="form-group has-feedback">
                        <input type="text" id="email" name="email" class="form-control" placeholder="Email/Username" value="<?php echo $username; ?>" class="login username-field" autocomplete="off">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" value="<?php echo $password; ?>" class="login password-field" autocomplete="off">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
<!--                            <div class="checkbox icheck">
                                <label>
                                    <input type="checkbox"> Remember Me
                                </label>
                            </div>-->
                        </div><!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo clang::__('Sign In'); ?></button>
                        </div><!-- /.col -->
                    </div>
                </form>

<!--                <div class="social-auth-links text-center">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
                    <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
                </div> /.social-auth-links 

                <a href="#">I forgot my password</a><br>
                <a href="register.html" class="text-center">Register a new membership</a>-->

            </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->

        <!-- Load javascript here -->
        <script src="<?php echo curl::base(); ?>media/js/libs/jquery.js"></script>
        <script src="<?php echo curl::base(); ?>media/js/libs/jquery.ui.custom.js"></script>


        <script src="<?php echo curl::base(); ?>media/js/libs/bootstrap.min.js"></script>


        <script src="<?php echo curl::base(); ?>media/js/cresenity.setup.js"></script>
        <script src="<?php echo curl::base(); ?>media/js/cresenity.message.js"></script>
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

            jQuery(document).ready(function ()
            {
                /*
                 * JS login effect
                 * This script will enable effects for the login page
                 */
                // Elements
                var doc = jQuery('html').addClass('js-login'),
                        container = jQuery('#container'),
                        formLogin = jQuery('#form-login'),
                        // If layout is centered
                        centered;



                /*
                 * AJAX login
                 * This function will handle the login process through AJAX
                 */
                formLogin.submit(function (event)
                {
                    // Values
                    var login = jQuery.trim(jQuery('#email').val()),
                            pass = jQuery.trim(jQuery('#password').val()),
                            captcha = jQuery.trim(jQuery('#captcha').val());

                    // Check inputs
                    if (login.length === 0) {
                        // Remove previous messages
                        formLogin.clearMessages();
                        // Display message
                        displayError('Please fill in your username');
                        return false;
                    } else if (pass.length === 0) {
                        // Remove previous messages
                        formLogin.clearMessages();
                        // Display message
                        displayError('Please fill in your password');
                        return false;
                    } else {
                        // Remove previous messages
                        formLogin.clearMessages();

                        // Show progress
                        displayLoading('Checking credentials...');


                        event.preventDefault();

                        // Stop normal behavior
                        event.preventDefault();

                        /*
                         * This is where you may do your AJAX call, for instance:
                         */
                        var url = '<?php echo curl::base(); ?>index.php/cresenity/login';

                        jQuery.ajax(url, {
                            dataType: 'json',
                            type: 'POST',
                            data: $("#form-login").serialize(),
                            success: function (data) {
                                var result = data.result;
                                var message = data.message;
                                if (result == "OK") {
                                    document.location.href = '<?php echo curl::base() . curl::current() . CFRouter::$query_string; ?>';
                                } else {
                                    formLogin.clearMessages();
                                    displayError(message);
                                }

                            },
                            error: function () {
                                formLogin.clearMessages();
                                displayError('Error while contacting server, please try again');
                            }
                        });


                    }
                });



                // Handle resizing (mostly for debugging)
                function handleLoginResize()
                {
                    // Detect mode
                    centered = (container.css('position') === 'absolute');

                    // Set min-height for mobile layout
                    if (!centered)
                    {
                        container.css('margin-top', '');
                    } else
                    {
                        if (parseInt(container.css('margin-top'), 10) === 0)
                        {
                            centerForm(false);
                        }
                    }
                }
                ;

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
                        var siblings = formLogin.siblings(),
                                finalSize = formLogin.outerHeight();

                        // Ignored elements
                        if (ignore)
                        {
                            siblings = siblings.not(ignore);
                        }

                        // Get other elements height
                        siblings.each(function (i)
                        {
                            finalSize += $(this).outerHeight(true);
                        });

                        // Setup
                        container[animate ? 'animate' : 'css']({marginTop: -Math.round(finalSize / 2) + 'px'});
                    }
                }
                ;

                // Initial vertical adjust
                centerForm(false);

                /**
                 * Function to display error messages
                 * @param string message the error to display
                 */
                function displayError(message)
                {
                    // Show message
                    var message = formLogin.message(message, {
                        append: false,
                        arrow: 'none',
                        classes: ['red-gradient'],
                        animate: false					// We'll do animation later, we need to know the message height first
                    });

                    // Vertical centering (where we need the message height)
                    centerForm(true, 'fast');

                    // Watch for closing and show with effect
                    message.bind('endfade', function (event)
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
                    var message = formLogin.message('<div class="alert alert-warning alert-dismissable loading">' + message + '</div>', {
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
                    message.bind('endfade', function (event)
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