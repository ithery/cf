<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 24, 2018, 7:01:57 PM
 */
?>

<div id="container-<?php echo $id ?>" class="fileupload fileupload-new" >
    <div class="fileupload-new thumbnail" >
        <img id="cimg-<?php echo $id; ?>" src="<?php echo $imgSrc; ?>" style="max-width: <?php echo $maxWidth; ?>px; max-height: <?php echo $maxHeight; ?>px;"  />
    </div>
    <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: <?php echo $maxWidth; ?>px; max-height: <?php echo $maxHeight; ?>px; line-height: 20px;"></div>'
    <div>
        <?php if (!$disabledUpload): ?>
            <span class="btn btn-file btn-primary">
                <span class="fileupload-new"><?php echo clang::__('Select Image'); ?></span>
                <span class="fileupload-change fileupload-exists"><?php echo clang::__('Change'); ?></span>
                <input type="file" accept="image/*" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" style="display:none;" />
            </span>
            <a href="javascript:;" class="btn fileupload-remove fileupload-exists btn-danger" data-dismiss="fileupload"><?php echo clang::__('Remove'); ?></a>
        <?php endif; ?>
    </div>
</div>



<script>




    $('#container-<?php echo $id ?> img, #container-<?php echo $id ?> .btn-file span').click(function () {
        $('#<?php echo $id; ?>').trigger('click');
    });

    $('#container-<?php echo $id ?> .fileupload-remove').click(function () {
        $('#container-<?php echo $id ?> .fileupload-preview').html('');
        $('#container-<?php echo $id ?>').removeClass('fileupload-exists');
        $('#container-<?php echo $id ?>').addClass('fileupload-new');
    });

    $('#<?php echo $id; ?>').change(function (e) {


        $.each(e.target.files, function (i, file) {
            var reader = new FileReader();
            reader.fileName = file.name;
            reader.onload = function (event) {
                if (file.type.match("image.*")) {
                    var img = "<img src=" + event.target.result + " /> ";
                    $('#container-<?php echo $id ?> .fileupload-preview').html(img);
                    $('#container-<?php echo $id ?>').removeClass('fileupload-new');
                    $('#container-<?php echo $id ?>').addClass('fileupload-exists');
                }
            };
            reader.readAsDataURL(file);
        });

    });
</script>
