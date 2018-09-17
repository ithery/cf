<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 8:23:43 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon-terminal"></i></span>
                <h5>DataBase Console</h5>

            </div>
            <div class="widget-content nopadding">
                <div id="mysql_console" style="height:400px"></div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        var idjrpc = 1;
        $('#mysql_console').terminal(function (command5, term) {
            term.pause();

            $.jrpc("<?php echo curl::base(); ?>admin/core/db_rpc", idjrpc++, 'query', [command5], function (data) {
                term.resume();
                if (data.error) {
                    term.error(data.error.message);
                } else {
                    if (typeof data.result == 'boolean') {
                        term.echo(data.result ? 'success' : 'fail');
                    } else {
                        var len = data.result.length;
                        for (var i = 0; i < len; ++i) {
                            p = new Array();
                            $.each(data.result[i], function (m, n) {
                                p.push(n);
                            });
                            term.echo(p.join(' | '));
                        }
                    }
                }
            }, function (xhr, status, error) {
                term.error('[AJAX] ' + status + ' - Server reponse is: \n' + xhr.responseText);
                term.resume();
            });
        }, {
            prompt: '>',
            greetings: "Welcome to DB Console"
        });
    });
</script>