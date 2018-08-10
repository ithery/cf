<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<div id="container-<?= $id ?>" class="dialog-select dialog-select-new">
	<div class="dialog-select-new thumbnail" style="width: <?= $width ?>px; height: <?= $height ?>px;">
		<div class="square">
			<div class="square-content">
				<div class="image-absolute-wrapper">
					<img id="cimg-<?= $id ?>" src="<?= $imgSrc ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="dialog-select-preview dialog-select-exists thumbnail" style="width: <?= $width ?>px; height: <?= $height ?>px;">
		
	</div>
	<div>
		<span class="btn btn-primary btn-dialog-select">
			<span class="dialog-select-new"><?= clang::__($buttonLabel) ?></span>
			<span class="dialog-select-change dialog-select-exists">
				<?= clang::__('Change') ?>
			</span>
			<input type="hidden" id="<?= $id ?>" name="<?= $name ?>" value="<?= $value ?>">
		</span>
		<a href="javascript:;" class="btn btn-danger dialog-select-remove dialog-select-exists" data-dismiss="dialog-select"><?= clang::__('Remove') ?></a>
	</div>
</div>

<div id="modal-dialog-select-<?= $id ?>" class="modal">
    <div class="modal-dialog">
    	<div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h3>Please choose an Item</h3>
                <a href="#" class="close">&times;</a><span class="loader"></span>
            </div>
            <div class="modal-body opened">
            	<div class="dialog-select-search" style="margin-bottom: 20px">
            		<input type="text" name="search">
            	</div>
            	<div class="dialog-select-item-list" style="
				    display: grid;
				    grid-template-columns: repeat(5, 1fr);
				    grid-template-rows: auto;
				    grid-gap: 10px;
				    height: 50vh;
				">
            		<?php foreach ($items as $key => $item): ?>
            			<div class="item" style="border: 2px solid">
            				<div class="item-name">
            					<?= carr::get($item, 'name') ?>
            				</div>
            			</div>
            		<?php endforeach ?>
            	</div>
            </div>
            <div class="modal-footer">
                <div class="row" id="actions">
                    <div class="col-md-12 docs-buttons">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-choose">
                            	<?= clang::__('Choose') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	$('.dialog-select img, .dialog-select .btn-dialog-select span').click(function() {
		var modalDialog = $('#modal-dialog-select-' + "<?= $id ?>");

		modalDialog.modal({backdrop: 'static', keyboard:false});
	});

	$('.dialog-select .dialog-select-remove').click(function() {
		$('.dialog-select .dialog-select-preview').html('');
		$('.dialog-select').removeClass('dialog-select-exists');
		$('.dialog-select').addClass('dialog-select-new');
	});

	(function ($) {
	    var modalDialog = $('#modal-dialog-select-' + "<?= $id ?>");
	    var time = <?= $delay ?>;

	    delay = (function() {
	    	var timer;

	    	return function(callback, delay) {
	    		if (timer != undefined) {
	    			clearTimeout(timer);
	    		}

	    		timer = setTimeout(callback, delay);
	    	};
	    })();

	    modalDialog.find('.dialog-select-search input').keyup(function() {
	    	var value = $(this).val();
	    	delay(function() {
	    		$.ajax("<?= $ajaxUrl ?>", {
	    			data: {
	    				keyword: value,
	    				page: 1,
	    			}
	    		}).done(function(data) {
	    		    modalDialog.find('.modal-body').addClass('loading spinner');
	    		}).always(function() {
	    			modalDialog.find('.modal-body').removeClass('loading spinner');
	    		});
	    	}, time);
	    });

	    modalDialog.find('.close').click(function (e) {
	        modalDialog.modal('hide');
	    });
	})(jQuery);
</script>