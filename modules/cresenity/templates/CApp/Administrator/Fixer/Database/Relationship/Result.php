<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 13, 2019, 1:05:15 AM
 */
?>

<div class="card mb-3">
    <div class="card-header with-elements">
        <div class="card-header-title"><?php echo $table; ?></div>
        <div class="card-header-elements ml-auto">
            <a class="btn btn-success" id="<?php echo $this->element()->id() . '-action'; ?>" data-table="<?php echo $table; ?>" href="javascript:;"><i class="fas fa-play"></i> Execute SQL</a>
        </div>
    </div>
    <div class="card-body overflow-auto">
        <?php echo $this->section('resultBody'); ?>

    </div>
</div>
<script>
    $('#<?php echo $this->element()->id() . '-action'; ?>').click(function () {
        var table = $(this).attr('data-table');
        var url = '<?php echo curl::base(); ?>administrator/app/fixer/database/relationship/execute/' + table;
        var container = $(this).closest('.card');
        (function (card) {
            cresenity.blockElement(card);
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url,
                success: function (response) {
                    if (response.errCode > 0) {
                        var alert = $('<div class="alert alert-dismissible alert-error" role="alert">');
                        alert.append('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>');
                        alert.append(response.errMessage);
                        card.find('.card-body').append(alert);
                    } else {
                        card.fadeOut('slow', function () {

                        });
                    }
                },
                error: function () {
                },
                complete: function () {
                    cresenity.unblockElement(card);
                },
            });
        })(container);
    });
</script>
