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
        $created = $user->created;
    }
    $app = CApp::instance();
    $org = $app->org();
    
    $user_img = "user.png";
    
    $user_img = curl::base() .'media/img/'.$user_img;
    $user_img_top_small = $user_img;
    $user_img_left_medium = $user_img;
    
    $color = ccfg::get("default-color");
    if (!isset($color)) {
        $color = 'blue-light';
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <?php echo $head_client_script; ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="<?php echo curl::base(); ?>media/img/favico.png">

        <link href="<?php echo curl::base(); ?>ccore/css/<?php echo $css_hash; ?>" rel="stylesheet">
    </head>
    <body class="skin-<?php echo $color; ?> sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <!-- Logo -->
                <a href="<?php echo curl::base(); ?>" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>I</b>APP</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>ITtron</b>APP</span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="javascript:void(0)" id="toggle-fullscreen">
                                    <i class="fa fa-arrows-alt"></i>
                                    &nbsp;
                                </a>
                            </li>
                            <?php if (ccfg::get("have_real_notification")): ?>
                            <li class="dropdown notifications-menu">
                                <?php 
                                    $total_notification = notification::get_count();
                                ?>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" state="default">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning lbl-total-notification"><?php $total_notification; ?></span>
                                </a>
                                <ul id="dropdown-menu-notification" class="dropdown-menu">
                                   
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if (ccfg::get("change_theme")): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <?php echo clang::__('Theme'); ?> <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php 
                                        $theme_list = ctheme::get_theme_list();
                                        foreach ($theme_list as $k => $v) {
                                            if ($k != ctheme::get_current_theme()) {
                                                echo '<li><a href="' .curl::base() .'cresenity/change_theme/' .$k .'">' .$v .'</a></li>';
                                            }
                                        }
                                    ?>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if (ccfg::get("multilang")): ?>
                            <li class="dropdown">
                                <?php 
                                    $active_lang = '';
                                    $dropdown_menu = '';
                                    $list = clang::get_lang_list();
                                    foreach ($list as $k => $v) {
                                        $active = "";
                                        $img = '';
                                        if ($k != 'default') {
                                            $img = '<img style="padding-right:2px;display:inline-block;margin-top:-3px;" src="' . curl::base() . 'media/img/flags/' . $k . '.gif" />';
                                        }
                                        if ($k == clang::getlang()) {
                                            $active = "active";
                                            $active_lang = $img .' ' .$v;
                                        }
                                        else {
                                            $dropdown_menu .= '<li class="' . $active . '"><a href="' . curl::base() . 'cresenity/change_lang/' . $k . '" hreflang="' . $k . '">' . $img . ' ' . $v . '</a></li>';
                                        }
                                        
                                    }
                                ?>
                                 <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <?php echo $active_lang; ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php echo $dropdown_menu; ?>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?php echo $user_img_top_small ?>" class="user-image" alt="User Image">
                                    <span class="hidden-xs"><?php echo $username; ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="<?php echo $user_img ?>" class="img-circle" alt="User Image">
                                        <p>
                                            <?php 
                                                echo $username; 
                                                if (isset($created)) {
                                                    echo 'Member since ' .date("M. Y", strtotime($created));
                                                }
                                            ?>
                                            <!--<small>Member since Nov. 2012</small>-->
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
                                    <li class="user-body">
                                        <div class="col-xs-6 text-center">
                                            <a href="<?php echo curl::base(); ?>account/change_password">Change Password</a>
                                        </div>
                                        <div class="col-xs-6 text-center">
                                            <a href="<?php echo curl::base(); ?>account/settings">My Setting</a>
                                        </div>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?php echo curl::base(); ?>account/profile" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?php echo curl::base(); ?>cresenity/logout" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->
<!--                            <li>
                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                            </li>-->
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?php echo $user_img_left_medium ?>" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <p><?php echo $username; ?></p>
                            <!--<a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
                        </div>
                    </div>
                    <!-- search form -->
                    <!--                    <form action="#" method="get" class="sidebar-form">
                                            <div class="input-group">
                                                <input type="text" name="q" class="form-control" placeholder="Search...">
                                                <span class="input-group-btn">
                                                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </form>-->

                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <?php
                        echo CNavigation::instance()->set_theme($theme)->render();
                    ?>
                </section>
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <section class="content-header">
                    <h1>
                    <?php if ($show_title): ?>
                                <?php echo $title ?>
                    <?php endif; ?>
                        &nbsp;
                    </h1>
                    <?php if ($show_breadcrumb): ?>
                            <?php
                            if ($breadcrumb == "") $breadcrumb = $title;
                            if (!is_array($breadcrumb)) $breadcrumb = array();
                            if (CFRouter::$controller != "home"):
                                ?>
                                <ol class="breadcrumb">
                                    <li><a href="<?php echo curl::base(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                                    <?php foreach ($breadcrumb as $k => $b) : ?>
                                        <li><a href="<?php echo $b ?>"><?php echo $k; ?></a></li>
                                    <?php endforeach; ?>
                                    <li class="active"><?php echo $title; ?></li>
                                </ol>
                                <?php
                            endif;
                            ?>
                        <?php endif; ?>
                </section>
                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <section class="col-md-12">
                <?php
                $msg = cmsg::flash_all();
                if (strlen($msg) > 0) {
                    echo '<div class="row"><div class="col-md-12">' . $msg . '</div></div>';
                }
                ?>
                        

