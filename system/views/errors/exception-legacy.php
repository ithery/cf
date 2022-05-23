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
        <title><?php echo c::e($error); ?></title>
    </head>
    <body>
        <div id="framework_error" style="width:42em;margin:20px auto;">
            <h3><?php echo c::e($error); ?></h3>
            <p><?php echo c::e($description); ?></p>
            <?php if (!CF::isProduction() || (isset($_GET['show_debug_error']) && $_GET['show_debug_error'] == '1') || (isset($show_debug_error) && $show_debug_error)): ?>
                <?php if (!empty($line) and !empty($file)): ?>
                    <p><?php echo c::__('core.error_file_line', ['file' => $file, 'line' => $line]); ?></p>
                <?php endif; ?>
                <p><code class="block"><?php echo c::e($message); ?></code></p>
                <?php if (!empty($trace)): ?>
                    <h3><?php echo c::__('core.stack_trace'); ?></h3>
                    <pre><?php echo c::e($trace); ?></pre>
                <?php endif; ?>
                <p class="stats"><?php echo c::__('core.stats_footer'); ?></p>
            <?php endif; ?>
        </div>
        <?php

        echo '<script>window.capp = ' . json_encode(c::app()->variables()) . ';</script>';
        $cresJs = curl::base() . 'media/js/cres/dist/cres.js?v=' . md5(CFile::lastModified(DOCROOT . 'media/js/cres/dist/cres.js'));
        echo "<script defer src=\"${cresJs}\"></script>";
        ?>
    </body>
</html>
