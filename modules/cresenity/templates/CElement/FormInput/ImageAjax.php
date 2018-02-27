<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 13, 2018, 10:58:37 AM
 * @license Ittron Global Teknologi <ittron.co.id>
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
                <input id="input-temp-<?php echo $id; ?>" type="file" name="input-temp-<?php echo $id; ?>" style="display:none;" />
                <input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
            </span>
            <a href="javascript:;" class="btn fileupload-remove fileupload-exists btn-danger" data-dismiss="fileupload"><?php echo clang::__('Remove'); ?></a>
        <?php endif; ?>
    </div>
</div>


<script>

    var <?php echo $id ?>HaveCropper = <?php echo ($cropper != null) ? 'true' : 'false' ?>;

<?php if ($cropper != null) : ?>

        var cropperWidth = parseFloat('<?php echo $cropper->getCropperWidth(); ?>');
        var cropperHeight = parseFloat('<?php echo $cropper->getCropperHeight(); ?>');
<?php endif; ?>


    $('#container-<?php echo $id ?> img, #container-<?php echo $id ?> .btn-file span').click(function () {
        $('#input-temp-<?php echo $id; ?>').trigger('click');
    });

    $('#container-<?php echo $id ?> .fileupload-remove').click(function () {
        $('#container-<?php echo $id ?> .fileupload-preview').html('');
        $('#container-<?php echo $id ?>').removeClass('fileupload-exists');
        $('#container-<?php echo $id ?>').addClass('fileupload-new');
    });

    $('#input-temp-<?php echo $id; ?>').change(function (e) {
        $.each(e.target.files, function (i, file) {
            var reader = new FileReader();
            reader.fileName = file.name;
            reader.onload = function (event) {

                if (file.type.match("image.*")) {

                    var haveCropper = <?php echo $id ?>HaveCropper;

                    if (haveCropper) {

                        reader.onloadend = (function (event) {

                            var cropperId = '<?php echo ($cropper == null) ? '' : $cropper->id(); ?>';
                            var cropperModal = $('#modal-cropper-' + cropperId);
                            var cropperImgInitialized = cropperModal.find('img.cropper-hidden');
                            if (cropperImgInitialized.length > 0) {
                                cropperImgInitialized.cropper("destroy");
                            }


                            var cropperImg = cropperModal.find('img');
                            cropperImg.attr('src', event.target.result);
                            cropperModal.modal('show');
                            cropperImg.cropper({
                                aspectRatio: cropperWidth / cropperHeight,
                                crop: function (e) {

                                }
                            });


                            cropperModal.find('.btn-crop').click(function () {
                                imageData = cropperImg.cropper('getCroppedCanvas').toDataURL();

                                var img = "<img src=" + imageData + " /> ";
                                $('#container-<?php echo $id ?> .fileupload-preview').html(img);
                                $('#container-<?php echo $id ?>').removeClass('fileupload-new');
                                $('#container-<?php echo $id ?>').addClass('fileupload-exists');
                                $('#container-<?php echo $id ?> .fileupload-preview').addClass('loading spinner');
                                var data = new FormData();

                                data.append('<?php echo $ajaxName; ?>[]', imageData);
                                data.append('<?php echo $ajaxName; ?>_filename[]', event.target.fileName);
                                var xhr = new XMLHttpRequest();
                                xhr.onreadystatechange = function () {
                                    if (this.readyState == 4 && this.status == 200) {
                                        var dataFile = JSON.parse(this.responseText);
                                        $('#<?php echo $id; ?>').val(dataFile.file_id);
                                        $('#container-<?php echo $id ?> .fileupload-preview img').attr('src', dataFile.url);
                                        $('#container-<?php echo $id ?> .fileupload-preview').removeClass('loading');
                                        $('#container-<?php echo $id ?> .fileupload-preview').removeClass('spinner');
                                    } else if (this.readyState == 4 && this.status != 200) {
                                    }
                                };
                                xhr.open("post", '<?php echo $ajaxUrl; ?>');
                                xhr.send(data);
                                $(this).closest('.modal').modal('hide');
                            });

                        });


                    } else {

                        var img = "<img src=" + event.target.result + " /> ";
                        $('#container-<?php echo $id ?> .fileupload-preview').html(img);
                        $('#container-<?php echo $id ?>').removeClass('fileupload-new');
                        $('#container-<?php echo $id ?>').addClass('fileupload-exists');
                        $('#container-<?php echo $id ?> .fileupload-preview').addClass('loading spinner');
                        var data = new FormData();
                        data.append('<?php echo $ajaxName; ?>[]', file);
                        var xhr = new XMLHttpRequest();
                        xhr.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                var dataFile = JSON.parse(this.responseText);
                                $('#<?php echo $id; ?>').val(dataFile.file_id);
                                $('#container-<?php echo $id ?> .fileupload-preview img').attr('src', dataFile.url);
                                $('#container-<?php echo $id ?> .fileupload-preview').removeClass('loading');
                                $('#container-<?php echo $id ?> .fileupload-preview').removeClass('spinner');
                            } else if (this.readyState == 4 && this.status != 200) {
                            }
                        };
                        xhr.open("post", '<?php echo $ajaxUrl; ?>');
                        xhr.send(data);
                    }
                }
            };
            reader.readAsDataURL(file);
        });
        $(this).val("");
    });
</script>

