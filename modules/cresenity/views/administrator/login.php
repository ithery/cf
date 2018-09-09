<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 12:09:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
$username = "";
$password = "";
if (isset($_GET["demo"])) {
    $username = "demo";
    $password = "demo";
}
$db = CDatabase::instance();
$org_id = ccfg::get('org_id');
$get_org_code = 'default';
$fav_img = curl::base() . "application/admincompromall/default/media/img/favicon_compro.jpeg";

if (ccfg::get('compromall_system')) {
    $imgsrc = curl::base() . "application/admincompromall/default/media/img/icon_compro_mall.png";
} else {
    $imgsrc = curl::base() . "media/img/cresenity-logo.png";
}
// $imgsrc = curl::base()."media/img/cresenity-logo.png";
if (strlen($org_id) > 0) {
    $get_org = cdbutils::get_row("SELECT * FROM org WHERE status>0 AND org_id = " . $db->escape($org_id));
    $get_org_code = cobj::get($get_org, 'code');
}
$real_path = CF::get_dir('application') . 'admincompromall' . DS . $get_org_code . DS . 'upload' . DS . 'logo' . DS;
$logo_path = preg_replace('#\\\#ims', DS, $real_path);

if (file_exists($logo_path . 'item_image' . DS . cobj::get($get_org_code, 'item_image'))) {
    $imgsrc = curl::base() . 'application' . DS . 'admincompromall' . DS . $get_org_code . DS . 'upload' . DS . 'logo' . DS . 'item_image' . DS . cobj::get($get_org, 'item_image');
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.reset.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/libs/bootstrap-4-material/bootstrap-material.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?php echo $fav_img; ?>">

        <link href="<?php echo curl::base(); ?>media/css/font-awesome.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/plugins/animate/animate.css" rel="stylesheet">

        <link href="<?php echo curl::base(); ?>media/css/cresenity.colors.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/icon/fontawesome-5-f.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.form.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.message.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.utilities.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.generic-element.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.block-arrow.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.main.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.login.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.bs4.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.main.bs4.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.login.bs4.css?v=<?php echo uniqid(); ?>" rel="stylesheet">

        <!-- JavaScript at bottom except for Modernizr -->
        <script src="<?php echo curl::base(); ?>media/js/libs/modernizr.custom.js"></script>

    </head>
    <body style="background-color: #f3f3f3;">



        <div class="account-container">
            <div class="logo">
                <img style= "max-width: 140px;" src="<?php echo $imgsrc ?>"/>
            </div>
            <div class="content clearfix" id="container">

                <form method="post" action="" id="form-login">

                    <h3 class="align-center"><?php echo clang::__('Administrator Area'); ?></h3>		

                    <div class="login-fields">
                        <h4 class="align-center"></h4>
                        <p>&nbsp;</p>


                        <div class="field field-group-password">
                            <div class="icon-register-password d-flex align-items-center text-center"><i class="fas fa-lock"></i></div>
                            <label for="password"><?php echo clang::__('Password'); ?>:</label>
                            <input type="password" id="password" name="password" value="<?php echo $password; ?>" placeholder="Password" class="login password-field" autocomplete="off"/>
                        </div> <!-- /password -->

                    </div> <!-- /login-fields -->

                    <div class="login-actions">

                        <span class="login-checkbox">
                            <!--
                            <input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
                            <label class="choice" for="Field"><?php echo clang::__('Keep me signed in'); ?></label>
                            -->
                        </span>

                        <button class="button btn-login btn btn-warning btn-large"><?php echo clang::__('Sign In'); ?></button>

                    </div> <!-- .actions -->



                </form>

            </div> <!-- /content -->
        </div> <!-- /account-container -->

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
                    var pass = jQuery.trim(jQuery('#password').val()),
                            captcha = jQuery.trim(jQuery('#captcha').val());

                    // Check inputs
                    if (pass.length === 0) {
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
                        var url = '<?php echo curl::base(); ?>index.php/administrator/auth/login';

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
                    var message = formLogin.message('<strong>' + message + '</strong>', {
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

                jQuery(".btn-forgot-password").click(function () {
                    jQuery('#email_forgot').val('');
                    jQuery('#modal_forgot_password').modal('show');
                    jQuery('.modal-info-loading').hide();
                    jQuery('.modal-info-msg').hide();
                });

                jQuery('.modal-info-msg').hide();
                jQuery('.modal-info-loading').hide();
                jQuery('.btn-reset-password').click(function () {
                    jQuery('.modal-info-msg').hide();
                    jQuery('.modal-info-loading').show('fast');
<?php if (isset($_SERVER['HTTPS'])) { ?>
                        var url_reset_password = 'https://<?php echo CF::domain(); ?>/setting_users/forgotpassword/index';
<?php } else { ?>
                        var url_reset_password = 'http://<?php echo CF::domain(); ?>/setting_users/forgotpassword/index';
<?php } ?>

                    $.ajax({
                        url: url_reset_password,
                        dataType: "json",
                        data: {
                            username: jQuery('#username_forgot').val()
                        },
                        // Work with the response
                        success: function (response) {
                            var err_code = response.error;
                            var err_message = response.message;
                            if (err_code > 0) {
                                // alert(err_message);
                                jQuery('.modal-info-loading').hide('fast');
                                jQuery('.modal-info-msg').html(err_message);
                                jQuery('.modal-info-msg').show('fast');
                                //hide_alert();
                            } else {
                                jQuery('.email-forgot-ok').html(response.email);
                                jQuery('#modal_forgot_password').modal('hide');
                                jQuery('#modal_forgot_ok').modal('show');
                            }
                        }
                    });
                });

            });

            // What about a notification?
            //notify('This is new demo of solusigps', 'Want to see another login page style? Try the <a href="login-box.html"><b>box version</b></a> or the <a href="login-full.html"><b>full version</b></a>.', {
            //	autoClose: false,
            //	delay: 2500,
            //	icon: 'img/demo/icon.png'
            //});

        </script>
        <div>

            <div style="clear:both;"></div>
        </div>
    </body>
</html>