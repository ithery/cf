<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 8:23:43 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
if(!isset($ajaxMethod)) {
    $ajaxMethod='post';
}
?>

<div id="<?php echo $elementId; ?>" style="height:<?php echo $height; ?>px"></div>

<script>

    $('#<?php echo $elementId; ?>').terminal(function (command, term) {
        term.pause();
        var url = '<?php echo $ajaxUrl; ?>';
        $.ajax({
            type: '<?php echo $ajaxMethod; ?>',
            dataType: 'text',
            url: url,
            data: {command:command},
            success: function (response) {
                term.echo(response);
            },
            error: function (xhr, status, error) {
                term.error('[AJAX] ' + status + ' - Server reponse is: \n' + xhr.responseText);
                term.resume();
            },
            complete: function () {
                term.resume();
            }
        });

    }, {
        prompt: '<?php echo $prompt; ?>',
        greetings: "<?php echo $greetings; ?>"
    });

</script>