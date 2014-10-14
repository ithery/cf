<?php
defined('SYSPATH') OR die('No direct access allowed.');


$app = CApp::instance();
$org = $app->org();

//$logo_image = $org->logo_image;
$logo_image = "";
$logosrc = curl::base() . 'cresenity/noimage/80/40';
if (strlen($logo_image) > 0) {
    $logosrc = cimage::get_image_src("logo_image", $org->org_id, "original", $logo_image);
}
?>
<!DOCTYPE html>
<html class="no-js" lang="<?php echo clang::getlang(); ?>" >
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="<?php echo $logosrc; ?>">
        <link href="<?php echo curl::base(); ?>media/css/front/bootstrap.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/front/bootstrap-responsive.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/font-awesome.css" rel="stylesheet">

        <link href="<?php echo curl::base(); ?>media/css/front/style.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/front/style-responsive.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>media/css/front/header.css" rel="stylesheet">
        <link href="<?php echo curl::base(); ?>ccore/front_css" rel="stylesheet">

        <script src="<?php echo curl::base(); ?>media/js/front/modernizr.js"></script>
    </head>
    <body>
        <?php echo $custom_header; ?>
        <!--=== Top ===-->    
        <div class="top">
            <div class="container">         
                <ul class="loginbar pull-right">
                    <?php if (ccfg::get("multilang")): ?>
                        <li class="dropdown">

                            <a href="javascript:;">
                                <img style="padding-right:5px;display:inline-block;margin-top:-3px;" src="<?php echo curl::base(); ?>media/img/flags/<?php echo clang::getlang(); ?>.gif" />
                                <?php echo clang::current_lang_name(); ?>
                                <b class="caret"></b>
                            </a>

                            <ul class="nav-list">
                                <?php
                                $list = clang::get_lang_list();
                                foreach ($list as $k => $v) {
                                    $active = "";
                                    if ($k == clang::getlang())
                                        $active = "active";
                                    $img = '<img style="padding-right:10px;display:inline-block;margin-top:-3px;" src="' . curl::base() . 'media/img/flags/' . $k . '.gif" />';
                                    echo '<li class="' . $active . '"><a href="' . curl::base() . 'cresenity/change_lang/' . $k . '" hreflang="' . $k . '">' . $img . ' ' . $v . '</a></li>';
                                }
                                ?>

                            </ul>

                        </li>
                    <?php endif; ?>
                    <!--
        <li class="devider">&nbsp;</li>
        <li><a href="page_faq.html" class="login-btn">Help</a></li>  
        <li class="devider">&nbsp;</li>
        <li><a href="page_login.html" class="login-btn">Login</a></li>   
                    -->
                </ul>
            </div>      
        </div><!--/top-->
        <!--=== End Top ===-->    

        <!--=== Header ===-->
        <div class="header">               
            <div class="container"> 
                <!-- Logo -->       
                <div class="logo">

                    <a href="<?php echo curl::base(); ?>"><img id="logo-header" src="<?php echo $logosrc; ?>" alt="Logo"></a>
                </div><!-- /logo -->        

                <!-- Menu -->       
                <div class="navbar">                                
                    <div class="navbar-inner">                                  
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a><!-- /nav-collapse -->                                  
                        <div class="nav-collapse collapse">                                     
                            <?php
                            //echo cmenu::populate_menu($role_id);
                            echo CNavigation::instance()->render(3);
                            ?>
                            <!--
                            
                            
                            <ul class="nav top-2">
            <li class="active">
                <a href="<?php curl::base(); ?>" >Home</a>
                 
            </li>
            <li>
                <a href="" class="dropdown-toggle" data-toggle="dropdown">Pages
                    <b class="caret"></b>                            
                </a>
                <ul class="dropdown-menu">
                    <li><a href="page_about.html">About Us</a></li>
                    <li><a href="page_services.html">Services</a></li>
                    <li><a href="page_pricing.html">Pricing</a></li>
                    <li><a href="page_coming_soon.html">Coming Soon</a></li>
                    <li><a href="page_faq.html">FAQs</a></li>
                    <li><a href="page_search.html">Search Result</a></li>
                    <li><a href="page_gallery.html">Gallery</a></li>
                    <li><a href="page_registration.html">Registration</a></li>
                    <li><a href="page_login.html">Login</a></li>
                    <li><a href="page_404.html">404</a></li>
                    <li><a href="page_clients.html">Clients</a></li>
                    <li><a href="page_privacy.html">Privacy Policy</a></li>
                    <li><a href="page_terms.html">Terms of Service</a></li>
                    <li><a href="page_column_left.html">2 Columns (Left)</a></li>
                    <li><a href="page_column_right.html">2 Columns (Right)</a></li>
                </ul>
                <b class="caret-out"></b>                        
            </li>
            <li>
                <a href="" class="dropdown-toggle" data-toggle="dropdown">Features
                    <b class="caret"></b>                            
                </a>
                <ul class="dropdown-menu">
                    <li><a href="feature_grid.html">Grid Layout</a></li>
                    <li><a href="feature_typography.html">Typography</a></li>
                    <li><a href="feature_thumbnail.html">Thumbnails</a></li>
                    <li><a href="feature_component.html">Components</a></li>
                    <li><a href="feature_navigation.html">Navigation</a></li>
                    <li><a href="feature_table.html">Tables</a></li>
                    <li><a href="feature_form.html">Forms</a></li>
                    <li><a href="feature_icons.html">Icons</a></li>
                    <li><a href="feature_button.html">Buttons</a></li>
                </ul>
                <b class="caret-out"></b>                        
            </li>
            <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Portfolio
                    <b class="caret"></b>                            
                </a>
                <ul class="dropdown-menu">
                    <li><a href="portfolio.html">Portfolio</a></li>
                    <li><a href="portfolio_item.html">Portfolio Item</a></li>
                    <li><a href="portfolio_2columns.html">Portfolio 2 Columns</a></li>
                    <li><a href="portfolio_3columns.html">Portfolio 3 Columns</a></li>
                    <li><a href="portfolio_4columns.html">Portfolio 4 Columns</a></li>
                </ul>
                <b class="caret-out"></b>                        
            </li>
            <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Blog
                    <b class="caret"></b>                            
                </a>
                <ul class="dropdown-menu">
                    <li><a href="blog.html">Blog</a></li>
                    <li><a href="blog_item.html">Blog Item</a></li>
                    <li><a href="blog_left_sidebar.html">Blog Left Sidebar</a></li>
                    <li><a href="blog_item_left_sidebar.html">Blog Item Left Sidebar</a></li>
                </ul>
                <b class="caret-out"></b>                        
            </li>
            <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Contact
                    <b class="caret"></b>                            
                </a>
                <ul class="dropdown-menu">
                    <li><a href="page_contact.html">Contact Default</a></li>
                    <li><a href="page_contact1.html">Contact Boxed Map</a></li>
                </ul>
                <b class="caret-out"></b>                        
            </li>
            <li><a class="search"><i class="icon-search search-btn"></i></a></li>                               
        </ul>
        <div class="search-open">
            <div class="input-append">
                <form>
                    <input type="text" class="span3" placeholder="Search" />
                    <button type="submit" class="btn-u">Go</button>
                </form>
            </div>
        </div>
                            -->
                        </div><!-- /nav-collapse -->                                
                    </div><!-- /navbar-inner -->
                </div><!-- /navbar -->                          
            </div><!-- /container -->               
        </div><!--/header -->      
        <!--=== End Header ===-->

        <?php if ($show_breadcrumb || $show_title): ?>
            <?php
            if ($breadcrumb == "")
                $breadcrumb = $title;
            if (!is_array($breadcrumb))
                $breadcrumb = array();
            if (Router::$controller != "home"):
                ?>
                <div class="row-fluid breadcrumbs margin-bottom-40">
                    <div class="container">
                        <?php if ($show_title): ?>
                            <h1 class="pull-left"><?php $title; ?></h1>
                        <?php endif; ?>
                        <?php if ($show_breadcrumb): ?>
                            <ul class="pull-right breadcrumb">
                                <li><a href="<?php echo curl::base(); ?>" class="tip-left" data-original-title="Go to Home"><i class="icon-home"></i> Home</a><span class="divider">/</span></li>
                                <?php foreach ($breadcrumb as $k => $b) : ?>
                                    <li><a href="<?php echo $b ?>" class=""><?php echo $k; ?></a><span class="divider">/</span></li>
                                <?php endforeach; ?>
                                <li class="active"><?php echo $title; ?></li>
                            </ul>
                        <?php endif; ?>
                    </div>
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

        <!--=== Content Part ===-->
        <div class="container">					