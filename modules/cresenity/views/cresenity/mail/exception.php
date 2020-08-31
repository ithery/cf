<?php
defined('SYSPATH') OR die('No direct access allowed.');
$app = CApp::instance();
$org = $app->org();
$user = $app->user();
$role = $app->role();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <style type="text/css">
            *, html { margin:0; padding:0;}
            table,tr,td,div,li,ul,a,p { margin:0; padding:0;}
            div#framework_error { background:#fff; border:solid 1px #ccc; font-family:sans-serif; color:#111; font-size:14px; line-height:130%; padding:0px 0px}
            div#framework_error .logo { text-align:center; }
            div#framework_error h3 { color:#fff; font-size:16px; padding:8px 6px; margin:0 0 8px; background:#000; text-align:center; }
            div#framework_error a { color:#228; text-decoration:none; }
            div#framework_error a:hover { text-decoration:underline; }
            div#framework_error strong { color:#900; }
            div#framework_error p strong { color:#000; }
            div#framework_error p { margin:0; padding:4px 6px 10px; }
            div#framework_error tt,
            div#framework_error pre,
            div#framework_error code { font-family:monospace; padding:2px 4px; font-size:12px; color:#333;
                                       white-space:pre-wrap; /* CSS 2.1 */
                                       white-space:-moz-pre-wrap; /* For Mozilla */
                                       word-wrap:break-word; /* For IE5.5+ */
            }
            div#framework_error tt { font-style:italic; }
            div#framework_error tt:before { content:">"; color:#aaa; }
            div#framework_error code tt:before { content:""; }
            div#framework_error pre,
            div#framework_error code { background:#eaeee5; border:solid 0 #D6D8D1; border-width:0 1px 1px 0; }
            div#framework_error .block { display:block; text-align:left; }
            div#framework_error .stats { padding:4px; background: #eee; border-top:solid 1px #ccc; text-align:center; font-size:10px; color:#888; }
            div#framework_error .backtrace { margin:0; padding:0; list-style:none; list-style-type:none; line-height:12px; }
            div#framework_error .backtrace li { list-style-type:none; margin:0; padding:0;}

        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $error ?></title>
        <base href="http://php.net/" />
    </head>
    <body>


        <div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td align="center" valign="top" style="padding:20px 0 20px 0" style="margin:0 auto;">
                        <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" >
                            <tr><td >
                                    <div id="framework_error" >
                                        <h3><?php echo chtml::specialchars('CRESENITY APP ERROR') ?></h3>
                                        <?php if ($org != null): ?>
                                            <p><strong>Name</strong>:<?php echo $org->name; ?></p>
                                        <?php endif ?>
                                        <?php if ($user != null): ?>
                                            <p><strong>Username</strong>:<?php echo $user->username; ?></p>
                                            <p><strong>Role name</strong>:<?php echo $role == null ? '' : $role->name; ?></p>
                                        <?php endif ?>
                                        <p><strong>Time</strong>:<?php echo date('Y-m-d H:i:s'); ?></p>
                                        <p><strong>Browser</strong>:<?php echo crequest::browser(); ?></p>
                                        <p><strong>Browser Version</strong>:<?php echo crequest::browser_version(); ?></p>
                                        <p><strong>Platform</strong>:<?php echo crequest::platform(); ?></p>
                                        <p><strong>Platform Version</strong>:<?php echo crequest::platform_version(); ?></p>
                                        <p><strong>User Agent</strong>:<?php echo crequest::user_agent(); ?></p>
                                        <p><strong>Remote Address</strong>:<?php echo crequest::remote_address(); ?></p>
                                        <p><strong>Complete Uri</strong>:<?php echo crouter::complete_uri(); ?></p>
                                        <p><strong>Controller</strong>:<?php echo crouter::controller(); ?></p>
                                        <p><strong>Method</strong>:<?php echo crouter::method(); ?></p>
                                        <h3><?php echo chtml::specialchars($error) ?></h3>
                                        <p><?php echo chtml::specialchars($description) ?></p>
                                        <?php if (!empty($line) AND!empty($file)): ?>
                                            <p><?php echo CF::lang('core.error_file_line', $file, $line) ?></p>
                                        <?php endif ?>
                                        <p>
                                            <code class="block"><?php echo $message ?></code>
                                        </p>

                                        <h3><?php echo 'Trace String' ?></h3>
                                        <?php echo $exception->getTraceAsString(); ?>

                                        <?php if (!empty($trace)): ?>
                                            <h3><?php echo CF::lang('core.stack_trace') ?></h3>
                                            <?php echo $trace ?>
                                        <?php endif ?>
                                </td></tr>
                        </table>
                    </td>
                </tr>
            </table>	

        </div>

    </body>
</html>