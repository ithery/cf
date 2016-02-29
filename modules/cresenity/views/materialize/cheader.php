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
}
$app = CApp::instance();
$org = $app->org();
?>
<!DOCTYPE html>
<html class="no-js" lang="<?php echo clang::getlang(); ?>" >
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <?php echo $head_client_script; ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="<?php echo curl::base(); ?>media/img/favico.png">

        <link href="<?php echo curl::base(); ?>ccore/css/<?php echo $css_hash; ?>" rel="stylesheet">
		<?php //echo $additional_head;?>
    </head>
    <body>
        <?php echo $custom_header; ?>
        <?php echo $begin_client_script; ?>
		<nav class="nav-header" role="navigation">
			<div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo"><?php
                        $web_title = ccfg::get("title");
                        $app = MApp::instance();
                        if(strlen($app->get_title()) > 0) {
                        	$web_title = $app->get_title();
                        }
                        //if($org!=null) $web_title = strtoupper($org->name);
                        echo $web_title;
                        ?></a>
				<ul class="right ">
					<li><a href="#"><i class="material-icons">search</i></a></li>
					<li><a href="#"><i class="material-icons">shopping_cart</i></a></li>
				</ul>
                <ul id="dropdown1" class="dropdown-content">
                  <li><a href="#!">one</a></li>
                  <li><a href="#!">two</a></li>
                  <li class="divider"></li>
                  <li><a href="#!">three</a></li>
                </ul>
				<div id="nav-mobile" class="side-nav fixed">
        <div class="menu-container">
          <ul class="menu-list top-menu">
            <li>
              <a id="logo-container" href="http://materializecss.com/" class="sidenav-top" style="background-image: url(http://mobile.62hallfamily.local/media/img/sidenav.png);">
                <div class="sidenav-account-picture">
                  <img class="img-circle" src="http://lorempixel.com/50/50/nature/2">
                </div>
                <div class="sidenav-account-id">
                  <p id="account-name">Robin Yonathan</p>
                  <p id="account-email">robinyonathan@yahoo.com</p>
                </div> 
                <div class="sidenav-account-setting">
                  <i class="material-icons">settings</i>
                </div> 
                <!-- <img src="https://www.google.co.id/logos/doodles/2016/leap-year-2016-5690429188079616-res.png" alt=""> -->
              </a>
            </li>
  					<li><a href="/demo/typography" class="sidenav-menu"><div class="material-icons nav-icons">shopping_cart</div><div>Typography</div></a></li>
  					<li><a href="/demo/action" class="sidenav-menu"><div class="material-icons nav-icons">shopping_cart</div><div>Action</div></a></li>
  					<li><a href="/demo/form" class="sidenav-menu"><div class="material-icons nav-icons">shopping_cart</div><div>Form</div></a></li>
  					<li><a href="/demo/media" class="sidenav-menu" ><div class="material-icons nav-icons">shopping_cart</div><div>Media</div></a></li>
  					<li>
              <!-- <a class="sidenav-back-button">Back</a> -->
              <a class="sidenav-header" id="4565657"><div class="material-icons nav-icons">shopping_cart</div>Card</a>
              <div class="sidenav-body">
                  <ul class="menu-list">
                    <div class="sidenav-back-button-block"><a class="sidenav-back-button"><i class="material-icons">arrow_back</i></a> </div>
                    <li>
                      <a class="sidenav-header" class="sidenav-menu" id="45657">Card1</a>
                      <div class="sidenav-body">
                          <div class="sidenav-back-button-block"><a class="sidenav-back-button"><i class="material-icons left">arrow_back</i></a></div>
                          <ul>
                            <li><a href="badges.html">X</a></li>
                            <li><a href="buttons.html">F</a></li>
                            <li><a href="breadcrumbs.html">G</a></li>
                            <li><a href="cards.html">B</a></li>
                          </ul>
                      </div>
                    </li>
                    <li>
                      <a class="sidenav-header" class="sidenav-menu" id="45657">Card2</a>
                      <div class="sidenav-body">
                          <div class="sidenav-back-button-block"><a class="sidenav-back-button"><i class="material-icons">arrow_back</i></a></div>
                          <ul>
                            <li><a href="badges.html">Q</a></li>
                            <li><a href="buttons.html">W</a></li>
                            <li><a href="breadcrumbs.html">E</a></li>
                            <li><a href="cards.html">R</a></li>
                          </ul>
                      </div>
                    </li>
                    <li></li>
                    <li><a href="badges.html">Badges</a></li>
                    <li><a href="buttons.html">Buttons</a></li>
                    <li><a href="breadcrumbs.html">Breadcrumbs</a></li>
                    <li><a href="cards.html">Cards</a></li>
                    <li><a href="chips.html">Chips</a></li>
                    <li><a href="collections.html">Collections</a></li>
                    <li><a href="footer.html">Footer</a></li>
                    <li><a href="forms.html">Forms</a></li>
                    <li><a href="icons.html">Icons</a></li>
                    <li><a href="navbar.html">Navbar</a></li>
                    <li><a href="pagination.html">Pagination</a></li>
                    <li><a href="preloader.html">Preloaderx</a></li>
                  </ul>
              </div>
            </li>
            <li><a href="preloader.html"><div class="material-icons nav-icons">shopping_cart</div>Preloader</a></li>
            <li><a href="preloader.html"><div class="material-icons nav-icons">shopping_cart</div>fasf</a></li>
            <li><a href="preloader.html"><div class="material-icons nav-icons">shopping_cart</div>Preddloader</a></li>
            <li><a href="preloader.html"><div class="material-icons nav-icons">shopping_cart</div>Predsloader</a></li>
  				</ul>
        </div>
				</div>
				<a href="javascript:;" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
			</div>
            <?php CMobile_Navigation::instance()->render();?>
		</nav>
		<div class="content">
