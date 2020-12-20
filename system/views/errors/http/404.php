<?php
defined('SYSPATH') or die('No direct access allowed.');

echo cdbg::getTraceString();
$username = '';
$password = '';
if (isset($_GET['demo'])) {
    $username = 'demo';
    $password = 'demo';
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <title>404 Page Not Found</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.reset.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/bootstrap-responsive.css" rel="stylesheet">

        <link href="<?php echo curl::base(); ?>media/css/font-awesome.css" rel="stylesheet">

        <link href="<?php echo curl::base(); ?>media/css/cresenity.colors.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.form.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.message.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.utilities.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.generic-element.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.block-arrow.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.main.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.login.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity/cresenity.bs3.css" rel="stylesheet">
        <!-- JavaScript at bottom except for Modernizr -->
        <script src="<?php echo curl::base(); ?>media/js/libs/modernizr.custom.js"></script>

    </head>

    <body class="gray-bg">


        <div class="middle-box text-center animated fadeInDown">
            <h1>404</h1>
            <h3 class="font-bold">Page Not Found</h3>

            <div class="error-desc">
                Sorry, but the page you are looking for has note been found. Try checking the URL for error, then hit the refresh button on your browser or try found something else in our app.

            </div>
        </div>

    </body>

</html>