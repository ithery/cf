<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<div class="ccard-item" data-id="<?= $id ?>" data-name="<?= $name ?>" data-image="<?= $imageUrl ?>">
	<div class="square">
		<div class="square-content">
			<div class="image-absolute-wrapper">
				<img src="<?= $imageUrl ?>">
			</div>
		</div>
	</div>
	<div class="ccard-item__name">
		<?= $name ?>
	</div>
</div>