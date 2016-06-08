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
    
    $app_code = CF::app_code();
    $org_code = CF::org_code();
    if (strlen($org_code) == 0) {
        $org_code = 'default';
    }
?>

<html>
    <head>
        <meta charset="utf-8" />
        <meta name="format-detection" content="telephone=no" />
        <meta name="msapplication-tap-highlight" content="no" />
        <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width" />
        <!-- This is a wide open CSP declaration. To lock this down for production, see below. -->
        <!--<meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline'; style-src 'self' 'unsafe-inline' 'unsafe-eval'; media-src *; script-src 'self' 'unsafe-inline' 'unsafe-eval' intern.local internmobile.local" />-->
        <title><?php echo $title; ?></title>
        <?php echo $head_client_script; ?>
        <link href="<?php echo curl::base(); ?>ccore/css/<?php echo $css_hash; ?>" rel="stylesheet">
        <script type="text/javascript">
            var baseUrl = '<?php echo curl::base();?>';
        </script>
    </head>
    <body>
        <div class="page-container">
            
        

