<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<style type="text/css">
	.modal-dialog-select .dialog-select-item-list {
		display: grid;
		grid-template-columns: repeat(5, 1fr);
		grid-template-rows: auto;
		grid-gap: 10px;
		max-height: 50vh;
	}

	.modal-dialog-select .dialog-select-search {
		margin-bottom: 20px;
	}

	.modal-dialog-select .dialog-select-item-list .item {
		border: 2px solid green;
		cursor: pointer;
	}

	.modal-dialog-select .dialog-select-item-list .item.selected {
		border-color: blue;
	}

	.modal-dialog-select .dialog-select-load-more {
		display: none;
	}

	.modal-dialog-select .dialog-select-load-more.processing {
		display: block;
	}
</style>

<div id="container-<?= $id ?>" class="dialog-select <?= ($value) ? 'dialog-select-exists' : 'dialog-select-new'; ?>">
	<div class="dialog-select-new" style="width: <?= $width ?>px; height: <?= $height ?>px;">
		<div class="dialog-select-image thumbnail">
			<div class="square">
				<div class="square-content">
					<div class="image-absolute-wrapper">
						<img src="<?= CApp_Base::noImageUrl() ?>">
					</div>
				</div>
			</div>
		</div>
		<div class="dialog-select-name">
			
		</div>
	</div>
	<div class="dialog-select-preview dialog-select-exists" style="width: <?= $width ?>px; height: <?= $height ?>px;">
		<div class="dialog-select-image thumbnail">
			<div class="square">
				<div class="square-content">
					<div class="image-absolute-wrapper">
						<img id="cimg-<?= $id ?>" src="<?= $imgSrc ?>">
					</div>
				</div>
			</div>
		</div>
		<div class="dialog-select-name">
			
		</div>
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
    <div class="modal-dialog-select">
    	<div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h3>Please choose an Item</h3>
                <a href="#" class="close">&times;</a><span class="loader"></span>
            </div>
            <div class="modal-body opened">
            	<div class="dialog-select-search">
            		<input type="text">
            	</div>
            	<div class="dialog-select-item-list">

            	</div>
            	<div class="dialog-select-load-more">
            		
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

	$(".dialog-select .dialog-select-remove").click(function() {
		$("#container-<?= $id ?> .dialog-select-preview .dialog-select-name").html('');
		$("#container-<?= $id ?> input").val('');
		$("#container-<?= $id ?>").removeClass('dialog-select-exists');
		$("#container-<?= $id ?>").addClass('dialog-select-new');
	});

	$('#modal-dialog-select-' + "<?= $id ?>").find('.btn-choose').click(function() {
		var modalDialog = $('#modal-dialog-select-' + "<?= $id ?>");

		var selected = modalDialog.find('.item.selected');

		if (selected.length) {
			var input = $("#container-<?= $id ?> input");
			var preview = $("#container-<?= $id ?> .dialog-select-preview .dialog-select-name");

			input.val(selected.attr('data-id'));
			preview.html(selected.attr('data-name'));

			$("#container-<?= $id ?>").removeClass('dialog-select-new');
			$("#container-<?= $id ?>").addClass('dialog-select-exists');

			modalDialog.modal('hide');
		} else {
			alert('You have not choose an item');
		}
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

	    addLoading = (function(target) {
	    	modalDialog.find(target).addClass('loading spinner');
	    });

	    removeLoading = (function(target) {
	    	modalDialog.find(target).removeClass('loading spinner');
	    })

	    ajaxLoadItemList = (function(keyword = '', page = 1) {
	    	return function() {
	    		$.ajax("<?= $ajaxUrl ?>", {
	    			dataType: 'json',
	    			data: {
	    				keyword: keyword,
	    				page: page,
	    			}
	    		}).done(function(data) {
	    		    var result = data.data.result;

	    		    var list = modalDialog.find('.dialog-select-item-list');
	    		    list.empty();
	    		    list.append(result);
	    		}).always(function() {
	    			removeLoading('.dialog-select-item-list');

	    			modalDialog.find('.item').off('click').click(function (e) {
	    				if ($(this).hasClass('selected')) {
	    					$(this).removeClass('selected');
	    				} else {
	    					modalDialog.find('.item').removeClass('selected');
	    					$(this).addClass('selected');
	    				}
	    			});

	    			loadMore(keyword, ++page);
	    		});
	    	};
	    });

	    loadMore = (function() {
	    	var loader;

	    	return function(keyword = '', page = 1) {
	    		if (loader != undefined) {
	    			loader = undefined;
	    		}

	    		loader = (function() {
	    			modalDialog.find('.modal-body').scroll(function(e) {
	    				var scrollTop = $(this).scrollTop();
	    				var clientHeight = this.clientHeight;
	    				var scrollHeight = this.scrollHeight;

	    				if (scrollTop + clientHeight >= scrollHeight) {
	    					modalDialog.find('.dialog-select-load-more').addClass('processing');
	    					addLoading('.dialog-select-load-more');

	    					$.ajax("<?= $ajaxUrl ?>", {
	    						dataType: 'json',
	    						data: {
	    							keyword: keyword,
	    							page: page,
	    						}
	    					}).done(function(data) {
	    						var result = data.data.result;

	    						var list = modalDialog.find('.dialog-select-item-list');
	    						list.append(result);
	    					}).always(function() {
	    						modalDialog.find('.dialog-select-load-more').removeClass('processing');
	    						removeLoading('.dialog-select-load-more');

	    						modalDialog.find('.item').off('click').click(function (e) {
	    							if ($(this).hasClass('selected')) {
	    								$(this).removeClass('selected');
	    							} else {
	    								modalDialog.find('.item').removeClass('selected');
	    								$(this).addClass('selected');
	    							}
	    						});

	    						loadMore(keyword, ++page);
	    					});
	    				}
	    			});
	    		});

	    		loader();
	    	};
	    })();

	    $('.dialog-select-item-list').ready(function() {
	    	addLoading('.dialog-select-item-list');
	    	ajaxLoadItemList()();
	    })

	    modalDialog.find('.dialog-select-search input').keyup(function() {
	    	addLoading('.dialog-select-item-list');
	    	delay(ajaxLoadItemList($(this).val()), time);
	    });

	    modalDialog.find('.close').click(function (e) {
	        modalDialog.modal('hide');
	    });
	})(jQuery);
</script>