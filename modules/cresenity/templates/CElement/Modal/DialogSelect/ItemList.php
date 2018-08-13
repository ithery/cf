<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<?php foreach ($items as $key => $item): ?>

<div class="item" data-id="<?= carr::get($item, 'id') ?>" data-name="<?= carr::get($item, 'name') ?>">
	<div class="square">
		<div class="square-content">
			<div class="image-absolute-wrapper">
				<img src="<?= carr::get($item, 'imageUrl') ?>">
			</div>
		</div>
	</div>
	<div class="item-name">
		<?= carr::get($item, 'name') ?>
	</div>
</div>

<?php endforeach ?>