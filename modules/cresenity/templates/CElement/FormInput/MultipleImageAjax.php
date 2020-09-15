<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 13, 2018, 10:58:37 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
?>
<style>
    .container-multi-image-ajax.disabled {
        background:#CDCDCD;
        cursor: not-allowed;
    }
    .multi-image-ajax {
        border: 2px dashed #CDCDCD;
        margin-left: 0px;
        padding: 10px 10px;
        width: 100%;
        min-height: 100px;
    }
    .multi-image-ajax.ondrag {
        border: 5px dashed #CDCDCD;
        background-color: rgba(0, 0, 0, 0.05);
    }
    .multi-image-ajax div.container-file-upload {
        border: 1px solid #ddd;
        margin: 6px 6px;
        width: 200px;
        height: auto;
        float: left;
        text-align: center;
        position: relative;
        border-radius: 4px;
        padding:4px;
        background:#fff;
    }

    .multi-image-ajax .div-img{
        border: 0px;
        width: auto;
        height: 100px;
        margin: 10px 0px;
        position:relative;
    }
    .multi-image-ajax div img {
        position: absolute;
        width: auto;
        max-width: 100%;
        height: auto;
        max-height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;
        min-height: 1px;
    }
    .multi-image-ajax-description {
        text-align: center;
        display: block;
        font-size: 20px;
        padding: 15px 0;
    }
    .multi-image-ajax-message {
        margin-left: 0px;
        display: none;
        font-size: 20px;
    }
    .multi-image-ajax-file.loading .multi-image-ajax-loading {
        display: block;
    }
    .multi-image-ajax-file .multi-image-ajax-loading {
        display: none;
    }
    .multi-image-ajax_loading {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1000;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .multi-image-ajax-remove {
        cursor: pointer;
    }
    .div-custom-control{
        margin-top:10px;
    }
    .multi-image-ajax-btn-upload {
        width: 100%;
        margin: 15px 0;
    }
    .div-custom-control label{
        display:inline-block;
    }
    .div-custom-control input[type="text"]{
        display:inline-block;
        width:auto;
    }

    @media (min-width: 768px) {
        .multi-image-ajax-btn-upload {
            display: none;
        }
    }
</style>

<div id="container-<?php echo $id ?>" class="container-multi-image-ajax" data-maximum="<?php echo $maximum; ?>" >
    <input id="<?php echo $id ?>_input_temp" type="file" accept="<?php echo $accept;?>" name="<?php echo $id ?>_input_temp[]" class="multi-image-ajax-input-temp"  style="display:none;">
    <div id="<?php echo $id ?>_message" class="row alert alert-danger fade in multi-image-ajax-message">
    </div>
    <div id="<?php echo $id ?>_description" class="multi-image-ajax-description"><?php echo clang::__('Click or Drop Files On Box Below')?></div>
    <div id="<?php echo $id ?>" class="row control-fileupload multi-image-ajax">
        <?php
        foreach ($files as $f) :
            $input_name = carr::get($f, 'input_name');
            $input_value = carr::get($f, 'input_value');
            $file_url = carr::get($f, 'file_url');
            ?>
            <div class="multi-image-ajax-file container-file-upload">
                <div class="div-img">
                    <img src="<?php echo $file_url; ?>" />
                    <input type="hidden" name="<?php echo $name; ?>[<?php echo $input_name; ?>]" value="<?php echo $input_value; ?>">
                </div>
                <?php
                foreach ($customControl as $cc) :
                    $control = carr::get($cc, 'control');
                    $control_name = carr::get($cc, 'input_name');
                    $control_label = carr::get($cc, 'input_label');
                    //get value
                    $control_value_array = carr::get($customControlValue, $input_name, array());
                    $control_value = carr::get($control_value_array, $control_name);
                    ?>
                    <div class="div-custom-control">
                        <label><?php echo $control_label; ?>:</label><input type="<?php echo $control; ?>" name="<?php echo $name; ?>_custom_control[<?php echo $input_name; ?>][<?php echo $control_name; ?>]" value="<?php echo $control_value; ?>"  >
                    </div>
                <?php endforeach; ?>
                <a class="multi-image-ajax-remove">Remove</a>
            </div>
        <?php endforeach; ?>





    </div>
    <div>
        <div class="multi-image-ajax-btn-upload btn btn-success"><?php echo clang::__('Upload Image'); ?></div>
    </div>

</div>
<script>

    (function ($) {
        $(function () {
            var haveCropper = <?php echo ($cropper != null) ? 'true' : 'false' ?>;
            var maxUploadSize = <?= $maxUploadSize ?> * 1024 * 1024;

<?php if ($cropper != null) : ?>
            var cropperWidth = parseFloat('<?php echo $cropper->getCropperWidth(); ?>');
            var cropperHeight = parseFloat('<?php echo $cropper->getCropperHeight(); ?>');
            var cropBoxResizable = <?php echo json_encode($cropper->getCropperResizable()); ?>;
<?php endif; ?>
            var index = 0;
            var descriptionElement = $("#container-<?php echo $id ?> .multi-image-ajax-description");
            $('#container-<?php echo $id ?> .multi-image-ajax-btn-upload').click(function () {
                $('#container-<?php echo $id ?> .multi-image-ajax-input-temp').trigger("click");
            });
            $(this).on({
                "dragover dragenter": function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                },
                "drop": function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                },
            })
            $(".container-file-upload").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
            function fileChanged() {
                var container = $('#container-<?php echo $id ?>');
                var maximum = parseInt(container.attr('data-maximum'));
                var fileCount = container.find('.multi-image-ajax-file').length;
                if (maximum > 0 && fileCount >= maximum) {
                    container.addClass('disabled');
                } else {
                    container.removeClass('disabled');
                }
                $("#<?= $id ?>").trigger('change');
            }
            fileChanged();
            // Remove File
            function fileUploadRemove(e) {

                $('#container-<?php echo $id ?> .multi-image-ajax-remove').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $(this).parent().remove();
                    fileChanged();
                })
            }

            function insertFile(reader, file, fileList, event) {
                var haveCropper = <?php echo ($cropper != null) ? 'true' : 'false' ?>;
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
                        cropperModal.modal({backdrop: 'static', keyboard: false});

                        if (typeof cropperModal.data('bs.modal') === 'undefined') {
                            cropperModal.modal('open');
                        }
                        cropperImg.cropper({
                            aspectRatio: cropperWidth / cropperHeight,
                            zoomOnWheel: false,
                            cropBoxResizable: cropBoxResizable,
                            ready: function(e) {
                                var canvasData = $(this).cropper('getCanvasData');
                                var cropBoxData = $(this).cropper('getCropBoxData');

                                if (canvasData.naturalWidth < cropperWidth && canvasData.naturalHeight < cropperHeight) {
                                    $(this).cropper('setCanvasData', {
                                        left: canvasData.width / 2 - canvasData.naturalWidth,
                                        top: canvasData.height / 2 - canvasData.naturalHeight,
                                        width: cropperWidth,
                                        height: cropperHeight
                                    });
                                }

                                if (canvasData.naturalWidth == cropperWidth && canvasData.naturalHeight == cropperHeight) {
                                    $(this).cropper('setCanvasData', {
                                        left: cropBoxData.left,
                                        top: cropBoxData.top,
                                        width: cropBoxData.width,
                                        height: cropBoxData.height
                                    });
                                }
                            },
                            crop: function (e) {

                            }
                        });
                        cropperModal.find('.btn-crop').data('file', file);
                        cropperModal.find('.btn-crop').data('event', event);
                        var clickAssigned = cropperModal.find('.btn-crop').attr('click-assigned');
                        if (!clickAssigned) {
                            cropperModal.find('.btn-crop').off('click');
                            cropperModal.find('.btn-crop').click(function () {
                                var fileRead = cropperModal.find('.btn-crop').data('file');

                                var fileEvent = cropperModal.find('.btn-crop').data('event');
                                cropperModal.find('.btn-crop').attr('click-assigned', '1');
                                var mime = 'image/png';
                                if (cropperImg.attr('src').indexOf('image/jpeg') >= 0) {
                                    mime = 'image/jpeg';
                                }

                                imageData = cropperImg.cropper('getCroppedCanvas', {width: cropperWidth, height: cropperHeight}).toDataURL(mime);

                                addFile(fileRead, fileList, fileEvent, imageData);
                                $(this).closest('.modal').modal('hide');
                            });
                        }

                    });


                } else {
                    addFile(file, fileList, event, event.target.result);
                }
            }

            function addFile(file, fileList, event, imageData) {
                var img = file.type.match("image.*") ? $("<img src=" + imageData + " /> ") : $("<img src='/media/img/icons/file.png' />");
                var div = $("<div>").addClass("multi-image-ajax-file container-file-upload");
                div.click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
                var div_img = $("<div>").addClass("div-img");
                div_img.append(img);
                div.append(div_img);

                var div_cc;
                var cc_label;
                var cc;
<?php
foreach ($customControl as $cc):
    $control = carr::get($cc, 'control');
    $control_name = carr::get($cc, 'input_name');
    $control_label = carr::get($cc, 'input_label');
    ?>
                    div_cc = $("<div>").addClass("div-custom-control");
                    cc_label = $("<label>").html("<?php echo $control_label; ?> :");
                    cc = $("<input type=\"<?php echo $control; ?>\" name=\"<?php echo $name; ?>_custom_control[" + index + "][<?php echo $control_name; ?>]\">");
                    div_cc.append(cc_label);
                    div_cc.append(cc);
                    div.append(div_cc);
<?php endforeach; ?>

<?php if ($removeLink): ?>

                    var remove = $("<a>").addClass("multi-image-ajax-remove").html("Remove");
                    div.append(remove);
<?php endif; ?>
                div.append("<img class=\"multi-image-ajax-loading\" src=\"<?php echo curl::base(); ?>media/img/ring.gif\" />");
                fileList.append(div.addClass("loading"));
                fileUploadRemove();
                var data = new FormData();
                data.append("<?php echo $name; ?>[]", imageData);

                data.append('<?php echo $ajaxName; ?>_filename[]', file.name);
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        var dataFile = JSON.parse(this.responseText);
                        div.removeClass("loading");
                        div.append("<input type=\"hidden\" name=\"<?php echo $name; ?>[" + index + "]\" value=" + dataFile.fileId + ">");
                        if(file.type.match("image.*")){
                            img.attr('src', data.url);
                        }
                        index++;
                        fileChanged();
                    } else if (this.readyState == 4 && this.status != 200) {
                        //div.remove();
                    }
                };
                xhr.open("post", "<?php echo $ajaxUrl; ?>");
                xhr.send(data);
            }

            fileUploadRemove();

            $("#<?php echo $id ?>").sortable();
            $("#<?php echo $id ?>").on({
                "dragover dragenter": function (e) {
                    $(this).addClass("ondrag");
                },
                "dragleave dragend": function (e) {
                    $(this).removeClass("ondrag");
                },
                "drop": function (e) {
                    $(this).removeClass("ondrag");
                    var container = $('#container-<?php echo $id ?>');
                    if (!container.hasClass('disabled')) {
                        $("#<?php echo $id ?>").sortable();
                        var dataTransfer = e.originalEvent.dataTransfer;
                        if (dataTransfer && dataTransfer.files.length) {
                            dataTransferFiles = dataTransfer.files;
                            if (haveCropper) {
                                if (dataTransfer.files.length > 1) {
                                    dataTransferFiles = [dataTransfer.files[0]]
                                }
                            }
                            e.preventDefault();
                            e.stopPropagation();
                            $("#container-<?php echo $id ?> .multi-image-ajax-description").remove();
                            $.each(dataTransferFiles, function (i, file) {
                                var reader = new FileReader();
                                reader.fileName = file.name;

                                reader.onload = $.proxy(function (file, fileList, event) {
                                    var filesize = event.total;
                                    var maxUploadSize = <?= $maxUploadSize ?> * 1024 * 1024;
                                    var limitFile = <?= $limitFile ?>;
                                    if (limitFile && $("#<?= $id ?>").children().length >= limitFile) {
                                        $.cresenity.message('', '<div class="alert alert-danger text-center"><b>Error:</b> Only ' + limitFile + ' image can be uploaded</div>', 'bootbox');
                                    } else {
                                        if (maxUploadSize && filesize > maxUploadSize) {
                                            $.cresenity.message('', '<div class="alert alert-danger text-center"><b>Error:</b> Image Size is more than ' + <?= $maxUploadSize ?> + ' MB</div>', 'bootbox');
                                        } else {
                                            insertFile(reader, file, fileList, event);
                                        }
                                    }
                                }, this, file, $("#<?php echo $id; ?>"));
                                reader.readAsDataURL(file);
                            });
                        }
                    }
                }
            })
            if (!haveCropper) {
                $("#<?php echo $id; ?>_input_temp").attr('multiple', 'multiple');
            }

            // Add Image by Click
            $("#<?php echo $id; ?>").click(function () {
                var container = $('#container-<?php echo $id ?>');
                if (!container.hasClass('disabled')) {
                    $("#<?php echo $id; ?>_input_temp").trigger("click");
                }
            })
            $("#<?php echo $id; ?>_input_temp").change(function (e) {
                $("#<?php echo $id; ?>_description").remove();
                $.each(e.target.files, function (i, file) {

                    var reader = new FileReader();

                    reader.fileName = file.name;
                    reader.onload = $.proxy(function (file, fileList, event) {
                        var filesize = event.total;
                        var maxUploadSize = <?= $maxUploadSize ?> * 1024 * 1024;
                        var limitFile = <?= $limitFile ?>;
                        if (limitFile && $("#<?= $id ?>").children().length >= limitFile) {
                            $.cresenity.message('', '<div class="alert alert-danger text-center"><b>Error:</b> Only ' + limitFile + ' image can be uploaded</div>', 'bootbox');
                        } else {
                            if (maxUploadSize && filesize > maxUploadSize) {
                                $.cresenity.message('', '<div class="alert alert-danger text-center"><b>Error:</b> Image Size is more than ' + <?= $maxUploadSize ?> + ' MB</div>', 'bootbox');
                            } else {
                                insertFile(reader, file, fileList, event);
                            }
                        }
                    }, this, file, $("#<?php echo $id; ?>"));
                    reader.readAsDataURL(file);
                })
                $(this).val("");
            })

        }); // end of document ready
    })(jQuery); // end of jQuery name space







</script>

