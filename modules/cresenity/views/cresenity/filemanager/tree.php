<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 2:40:06 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

?>

<!DOCTYPE html>
<html class="no-js material-style" lang="<?php echo clang::getlang(); ?>" >
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <?php echo $head_client_script; ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?php echo curl::base(); ?>ccore/css/<?php echo $css_hash; ?>" rel="stylesheet">
        <?php echo $additional_head; ?>
    </head>
    <body>
        <?php echo $custom_header; ?>
        <?php echo $begin_client_script; ?>

        <?php echo $content; ?>


        <script src="<?php echo curl::base(); ?>media/js/require.js"></script>
        <!-- Load javascript here -->

        <?php echo $end_client_script; ?>

        <script language="javascript">

<?php
echo $js;
echo $ready_client_script;
?>

            if (window) {
                window.onload = function () {
<?php echo $load_client_script; ?>
                }
            }
<?php echo $custom_js ?>
        </script>
        <?php echo $custom_footer; ?>
    </body>
</html>
