<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <style type="text/css">
            div#framework_error { background:#fff; border:solid 1px #ccc; font-family:sans-serif; color:#111; font-size:14px; line-height:130%; }
            div#framework_error h3 { color:#fff; font-size:16px; padding:8px 6px; margin:0 0 8px; background:#f15a00; text-align:center; }
            div#framework_error a { color:#228; text-decoration:none; }
            div#framework_error a:hover { text-decoration:underline; }
            div#framework_error strong { color:#900; }
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
            div#framework_error .backtrace { margin:0; padding:0 6px; list-style:none; line-height:12px; }
        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $error ?></title>
    </head>
    <body>
        <div id="framework_error" style="width:42em;margin:20px auto;">
            <h3><?php echo chtml::specialchars($error) ?></h3>
            <p><?php echo chtml::specialchars($description) ?></p>
            <?php if (!IN_PRODUCTION || (isset($_GET['show_debug_error']) && $_GET['show_debug_error'] == '1') || (isset($show_debug_error) && $show_debug_error)): ?>
                <?php if (!empty($line) and !empty($file)): ?>
                    <p><?php echo CF::lang('core.error_file_line', ['file' => $file, 'line' => $line]) ?></p>
                <?php endif ?>
                <p><code class="block"><?php echo $message ?></code></p>
                <?php if (!empty($trace)): ?>
                    <h3><?php echo CF::lang('core.stack_trace') ?></h3>
                    <pre>
                        <?php echo $trace ?>
                    </pre>
                <?php endif ?>
                <p class="stats"><?php echo CF::lang('core.stats_footer') ?></p>
            <?php endif; ?>
        </div>
    </body>
</html>
<?php
defined('SYSPATH') or die('No direct access allowed.');

$httpReferer = carr::get($_SERVER, 'HTTP_REFERER', '');

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
                            <tr>
                                <td >
                                    <div id="framework_error" >
                                        <h3><?php echo chtml::specialchars('CRESENITY APP ERROR') ?></h3>
                                        <p><strong>Domain</strong>:<?php echo CF::domain(); ?></p>

                                        <p><strong>Time</strong>:<?php echo date('Y-m-d H:i:s'); ?></p>
                                        <p><strong>Complete Uri</strong>:<?php echo crouter::complete_uri(); ?></p>
                                        <p><strong>Controller</strong>:<?php echo crouter::controller(); ?></p>
                                        <p><strong>Method</strong>:<?php echo crouter::method(); ?></p>
                                        <p><strong>Referer</strong>:<?php echo $httpReferer; ?></p>
                                        <p><strong>Post Data</strong>:<?php echo json_encode($_POST); ?></p>
                                        <h3><?php echo c::e($error) ?></h3>
                                        <p><?php echo c::e($description) ?></p>
                                        <?php if (!empty($line) and !empty($file)): ?>
                                            <p><?php echo CF::lang('core.error_file_line', ['file' => $file, 'line' => $line]) ?></p>
                                        <?php endif ?>
                                        <p>
                                            <code class="block"><?php echo $message ?></code>
                                        </p>

                                        <h3><?php echo 'Trace String' ?></h3>
                                        <?php echo nl2br($exception->getTraceAsString()); ?>

                                        <?php if (!empty($trace)): ?>
                                            <h3><?php echo CF::lang('core.stack_trace') ?></h3>
                                            <?php echo $trace; ?>
                                        <?php endif ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </div>

    </body>
</html>
