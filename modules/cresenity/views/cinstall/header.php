<?php
defined('SYSPATH') OR die('No direct access allowed.');
$config = CConfig::factory()->get_array();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?php echo curl::base(); ?>media/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/bootstrap-responsive.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.main.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/cresenity.validationEngine.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/theme/theme.grey.css" rel="stylesheet">
    </head>
    <body>
        <div id="header">
            <h1><a href="<?php echo curl::base(); ?>">Cresenity Admin</a></h1>		
        </div>

        <div id="sidebar">
            <ul>
                <li class="submenu<?php echo ($config["install-step"] == 1 ? " active" : ""); ?>"><a href="#"><i class="icon icon-home"></i> <span>Installation</span></a></li>
                <li class="submenu<?php echo ($config["install-step"] == 2 ? " active" : ""); ?>"><a href="#"><i class="icon icon-home"></i> <span>Database</span></a></li>
                <li class="submenu<?php echo ($config["install-step"] == 3 ? " active" : ""); ?>"><a href="#"><i class="icon icon-home"></i> <span>Admin Account</span></a></li>
                <li class="submenu<?php echo ($config["install-step"] == 4 ? " active" : ""); ?>"><a href="#"><i class="icon icon-home"></i> <span>Last Step</span></a></li>
                <li class="submenu<?php echo ($config["install-step"] == 5 ? " active" : ""); ?>"><a href="#"><i class="icon icon-home"></i> <span>Finish</span></a></li>

            </ul>

        </div>
        <div id="content">
            <div id="content-header">
                <h1><?php echo $title ?></h1>

            </div>
            <div id="breadcrumb">
                <a href="#" class="tip-bottom" data-original-title="Go to Home"><i class="icon-home"></i> Install</a>
                <a href="#" class="current">Step <?php echo $config["install-step"] ?></a>
            </div>
            <div class="container-fluid">
                <?php
                $msg = cmsg::flash_all();
                if (strlen($msg) > 0) {
                    echo '<div class="row-fluid"><div class="span12">' . $msg . '</div></div>';
                }
                ?>
					
