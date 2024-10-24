<?php

defined('SYSPATH') or die('No direct access allowed.');

$session = CSession::instance();
$app = CApp::instance();

$user = $app->user();
$username = '';
if ($user) {
    $username = $user->username;
}

$appImageUrl = curl::base() . 'media/img/cresenity-logo.png';
$appTitle = ccfg::get('title');
?>
<!DOCTYPE html>
<html class="no-js material-style layout-navbar-fixed layout-fixed" lang="<?php echo clang::getlang(); ?>" >
    <head>
        <meta charset="utf-8">
        <title>@CAppPageTitle</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="<?php echo curl::base(); ?>media/img/favico.png">
        @CAppStyles
    </head>
    <body>
        <?php echo $custom_header; ?>
        <?php echo $begin_client_script; ?>
        <?php
        $showNavigation = true;
        $pageWrapperAttr = '';
        ?>
        <div id="wrapper" class="layout-wrapper layout-2">
            <div class="layout-inner">
                <?php if ($showNavigation): ?>
                    <nav id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-sidenav-theme navbar-default navbar-static-side" role="navigation">
                        <div class="brand">
                            <span class="brand-logo">
                                <img src="<?php echo $appImageUrl; ?>" />
                            </span>
                            <a href="home/index.php" class="brand-name sidenav-text font-weight-normal ml-2"><?php echo $appTitle; ?></a>

                        </div>


                        <div class="sidenav-divider mt-0"></div>
                        <div class="sidenav-inner py-1 ps">
                            @CAppNav
                        </div> <!-- /sidebar-collapse -->
                    </nav> <!-- /nav -->
                <?php endif; ?>

                <div id="page-wrapper" class="layout-container" <?php echo $pageWrapperAttr; ?>>


                    <nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center container-p-x bg-navbar-theme" id="layout-navbar">
                        <a href="<?php echo curl::base(); ?>" class="navbar-brand brand d-lg-none py-0">
                            <span class="brand-logo">
                                <img src="<?php echo $appImageUrl; ?>" />
                            </span>
                            <span class="brand-name font-weight-normal ml-2"><?php echo $appTitle; ?></span>
                        </a>
                        <div class="layout-sidenav-toggle navbar-nav align-items-lg-center mr-auto">
                            <a class="nav-item nav-link px-0 ml-2 ml-lg-0" href="javascript:void(0)">
                                <i class="ion ion-md-menu text-large align-middle"></i>
                            </a>
                        </div>
                        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse" aria-expanded="false">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="navbar-collapse collapse " id="layout-navbar-collapse">
                            <!-- Divider -->
                            <hr class="d-lg-none w-100 my-2">


                            <div class="navbar-nav align-items-lg-center ml-auto">
                                <div class="navbar-messages nav-item  mr-lg-3">
                                    <a id="toggle-fullscreen" class="nav-link hide-arrow" href="#" >
                                        <i class="ion ion-ios-expand navbar-icon align-middle"></i>
                                        <span class="d-lg-none align-middle">&nbsp; Fullscreen</span>
                                    </a>

                                </div>


                                <?php if (isset($login_required) && $login_required): ?>
                                    <div class="navbar-user nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                            <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                                                <!--
                                                <img src="/products/appwork/v100/assets_/img/avatars/1.png" alt="" class="d-block ui-w-30 rounded-circle">
                                                -->
                                                <i class="ion ion-ios-person"></i>
                                                <span class="px-1 mr-lg-2 ml-2 ml-lg-0"><?php echo $username; ?></span>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="<?php echo curl::base(); ?>administrator/setting" class="dropdown-item">
                                                <i class="ion ion-md-settings text-lightest"></i> &nbsp; Account settings</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="<?php echo curl::base(); ?>administrator/auth/logout" class="dropdown-item">
                                                <i class="ion ion-ios-log-out text-danger"></i> &nbsp; Log Out</a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </nav>


                    <div class="main layout-content">
                        <div class="main-inner container-fluid flex-grow-1 container-p-y">
                            <div class="row page-heading">
                                <div class="col-lg-12">
                                    <div class="bg-lightest container-m--x container-m--y mb-3">
                                        <ol class="breadcrumb text-big container-p-x py-3 m-0">
                                            <li class="breadcrumb-item">
                                                <a href="<?php echo curl::base(); ?>" class="tip-bottom" data-original-title="Go to Home"><i class="ion ion-ios-home"></i></a>
                                            </li>
                                            <?php foreach ($breadcrumb as $k => $b) : ?>
                                                <li class="breadcrumb-item">
                                                    <a href="<?php echo $b ?>" class=""><?php echo $k; ?></a>
                                                </li>

                                            <?php endforeach; ?>
                                            <li class="breadcrumb-item active">
                                                <?php echo $title ?>
                                            </li>
                                        </ol>
                                        <hr class="m-0">
                                    </div>


                                    <h4 class="font-weight-bold py-3 mb-4">
                                        <?php echo $title ?>
                                    </h4>
                                </div>

                            </div>

                            <?php
                            $msg = cmsg::flash_all();
                            if (strlen($msg) > 0) {
                                echo '<div class="row-fluid"><div class="span12">' . $msg . '</div></div>';
                            }
                            ?>
                            <div class="wrapper wrapper-content ">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php if (!$showNavigation): ?>
                                            <div class="container">
                                        <?php endif; ?>
                                        <?php echo $content; ?>
                                        <?php if (!$showNavigation): ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <nav class="layout-footer footer bg-footer-theme">
                            <div class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3">
                                <div class="pt-3">
                                    <span class="footer-text font-weight-bolder"><?php echo date('Y'); ?> Ittron Global Teknologi</span> &copy;
                                </div>
                                <div>
                                    <a href="javascript:void(0)" class="footer-link pt-3">About Us</a>
                                    <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Help</a>
                                    <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Contact</a>
                                    <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Terms &amp; Conditions</a>
                                </div>
                            </div>
                        </nav>
                    </div>


                </div><!-- /page-wrapper -->
            </div><!-- /layout-inner -->
            <div class="layout-overlay layout-sidenav-toggle"></div>
        </div><!-- /layout-wrapper -->
        @CAppScripts

    </body>
</html>
s
