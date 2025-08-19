@php
$suffixWidth = '';
$lastMaxWidth = substr($maxWidth, -1);
if (is_numeric($lastMaxWidth)) {
    $suffixWidth = 'px';
}

$suffixHeight = '';
$lastMaxHeight = substr($maxHeight, -1);
if (is_numeric($lastMaxHeight)) {
    $suffixHeight = 'px';
}
$maxWidth .= $suffixWidth;
$maxHeight .= $suffixHeight;

$noImageUrl = CApp_Base::noImageUrl();

$class = 'fileupload-new';
if ($onExists) {
    $class = 'fileupload-exists';
}
@endphp

<div id="container-{{ $id }}" class="fileupload {{ $class }}" >
    <div class="fileupload-new thumbnail" >
        @if ($onExists)
        <img id="cimg-{{ $id }}" src="{{ $noImageUrl }}" style="max-width: <?php echo $maxWidth; ?>; max-height: <?php echo $maxHeight; ?>;"  />
        @else
        <img id="cimg-{{ $id }}" src="{{ $imgSrc }}" style="max-width: <?php echo $maxWidth; ?>; max-height: <?php echo $maxHeight; ?>;"  />
        @endif
    </div>
    <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: <?php echo $maxWidth; ?>; max-height: <?php echo $maxHeight; ?>; line-height: 20px;">
        @if ($onExists)
        <img id="cimg-{{ $id }}" src="{{ $imgSrc }}" style="max-width: <?php echo $maxWidth; ?>; max-height: <?php echo $maxHeight; ?>;"  />
        @endif
    </div>
    <div>
        @if(!$disabledUpload)
            <span class="btn btn-file btn-primary">
                <span class="fileupload-new">@lang('element/image.selectFile')</span>
                <span class="fileupload-change fileupload-exists">@lang('element/image.change')</span>
                <input id="input-temp-<?php echo $id; ?>" accept="<?php echo $accept; ?>" type="file" name="input-temp-<?php echo $id; ?>" style="display:none;" accept="image/*"/>
                <input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
            </span>
            <a href="javascript:;" class="btn fileupload-remove fileupload-exists btn-danger" data-dismiss="fileupload">@lang('element/image.remove')</a>
        @endif
    </div>
</div>


<script>

    var <?php echo $id; ?>HaveCropper = <?php echo ($cropper != null) ? 'true' : 'false'; ?>;
    var <?php echo $id; ?>maxUploadSize = <?php echo $maxUploadSize; ?> * 1024 * 1024;



    $('#container-<?php echo $id; ?> img, #container-<?php echo $id; ?> .btn-file span').click(function () {
        $('#input-temp-<?php echo $id; ?>').trigger('click');
    });

    $('#container-<?php echo $id; ?> .fileupload-remove').click(function () {
        $('#container-<?php echo $id; ?> .fileupload-preview').html('');
        $("#<?php echo $id; ?>").val('');
        $("#<?php echo $id; ?>").trigger('change');
        $('#container-<?php echo $id; ?>').removeClass('fileupload-exists');
        $('#container-<?php echo $id; ?>').addClass('fileupload-new');
    });

    $('#input-temp-<?php echo $id; ?>').change(function (e) {
<?php if ($cropper != null) : ?>
            var cropperWidth = parseFloat('<?php echo $cropper->getCropperWidth(); ?>');
            var cropperHeight = parseFloat('<?php echo $cropper->getCropperHeight(); ?>');
            var cropBoxResizable = <?php echo json_encode($cropper->getCropperResizable()); ?>;
<?php endif; ?>
        $.each(e.target.files, function (i, file) {
            var reader = new FileReader();
            reader.fileName = file.name;
            reader.onload = function (event) {
                var filesize = event.total;

                var maxUploadSize = <?php echo $id; ?>maxUploadSize;
                if (maxUploadSize && filesize > maxUploadSize) {
                    cresenity.message('', '<div class="alert alert-danger text-center"><b>Error:</b> Image Size is more than ' + <?php echo $maxUploadSize; ?> + ' MB</div>', 'bootbox');
                } else {
                    if (file.type.match("image.*")) {

                        var haveCropper = <?php echo $id; ?>HaveCropper;

                        if (haveCropper) {

                            reader.onloadend = (function (event) {

                                var cropperId = 'modal-cropper-<?php echo ($cropper == null) ? '' : $cropper->id(); ?>';
                                var cropperModal = $('#' + cropperId);
                                var cropperImgInitialized = cropperModal.find('img.cropper-hidden');
                                if (cropperImgInitialized.length > 0) {
                                    cropperImgInitialized.cropper("destroy");
                                }

                                let bootstrapModalOptions = {backdrop: 'static', keyboard: false};
                                var cropperImg = cropperModal.find('img');
                                cropperImg.attr('src', event.target.result);
                                if(window.bootstrap) {
                                    console.log(cropperId);
                                    var bootstrapCropperModal = new bootstrap.Modal(document.getElementById(cropperId), bootstrapModalOptions);
                                    bootstrapCropperModal.show();
                                } else {
                                    cropperModal.modal(bootstrapModalOptions);
                                }

                                cropperImg.cropper({
                                    aspectRatio: cropperWidth / cropperHeight,
                                    zoomOnWheel: false,
                                    cropBoxResizable: cropBoxResizable,
                                    ready: function(e) {
                                        var imgData = $(this).cropper('getImageData');
                                        var containerData = $(this).cropper('getContainerData');
                                        var cropBoxData = $(this).cropper('getCropBoxData');

                                        if (imgData.naturalWidth < cropperWidth && imgData.naturalHeight < cropperHeight) {
                                            $(this).cropper('setCanvasData', {
                                                left: containerData.width / 2 - imgData.naturalWidth / 2,
                                                top: containerData.height / 2 - imgData.naturalHeight / 2,
                                                width: imgData.naturalWidth,
                                                height: imgData.naturalHeight
                                            });
                                        }

                                        if (imgData.naturalWidth == cropperWidth && imgData.naturalHeight == cropperHeight) {
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

                                cropperModal.find('.btn-crop').click(function (e) {
                                    e.stopPropagation();
                                    var mime = 'image/png';
                                    if (cropperImg.attr('src').indexOf('image/jpeg') >= 0) {
                                        mime = 'image/jpeg';
                                    }


                                    let imageData = cropperImg.cropper('getCroppedCanvas', {width: cropperWidth, height: cropperHeight}).toDataURL(mime);

                                    var img = "<img src=" + imageData + " /> ";
                                    $('#container-<?php echo $id; ?> .fileupload-preview').html(img);
                                    $('#container-<?php echo $id; ?>').removeClass('fileupload-new');
                                    $('#container-<?php echo $id; ?>').addClass('fileupload-exists');
                                    $('#container-<?php echo $id; ?> .fileupload-preview').addClass('loading spinner');
                                    $('#container-<?php echo $id; ?> .fileupload-preview').find('img').click(function () {
                                        $('#input-temp-<?php echo $id; ?>').trigger('click');
                                    });
                                    var data = new FormData();

                                    data.append('<?php echo $ajaxName; ?>[]', imageData);
                                    data.append('<?php echo $ajaxName; ?>_filename[]', event.target.fileName);
                                    var xhr = new XMLHttpRequest();
                                    xhr.onreadystatechange = function () {
                                        if (this.readyState == 4 && this.status == 200) {
                                            var response = JSON.parse(this.responseText);
                                            cresenity.handleJsonResponse(response, function (dataFile) {
                                                $('#<?php echo $id; ?>').val(dataFile.fileId);
                                                $('#container-<?php echo $id; ?> .fileupload-preview img').attr('src', dataFile.url);
                                                $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('loading');
                                                $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('spinner');
                                                $('#<?php echo $id; ?>').trigger('change');
                                            });
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
                            $('#container-<?php echo $id; ?> .fileupload-preview').html(img);
                            $('#container-<?php echo $id; ?>').removeClass('fileupload-new');
                            $('#container-<?php echo $id; ?>').addClass('fileupload-exists');
                            $('#container-<?php echo $id; ?> .fileupload-preview').addClass('loading spinner');
                            $('#container-<?php echo $id; ?> .fileupload-preview').find('img').click(function () {
                                $('#input-temp-<?php echo $id; ?>').trigger('click');
                            });
                            var data = new FormData();
                            data.append('<?php echo $ajaxName; ?>[]', file);
                            data.append('<?php echo $ajaxName; ?>_filename[]', file.name);

                            var xhr = new XMLHttpRequest();
                            xhr.onreadystatechange = function () {
                                if (this.readyState == 4 && this.status == 200) {
                                    var dataFile = JSON.parse(this.responseText);
                                    $('#<?php echo $id; ?>').val(dataFile.fileId);
                                    $('#container-<?php echo $id; ?> .fileupload-preview img').attr('src', dataFile.url);
                                    $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('loading');
                                    $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('spinner');
                                    $('#<?php echo $id; ?>').trigger('change');
                                } else if (this.readyState == 4 && this.status != 200) {
                                }
                            };
                            xhr.open("post", '<?php echo $ajaxUrl; ?>');
                            xhr.send(data);
                        }
                    } else {
                        cresenity.message('', '<div class="alert alert-danger text-center"><b>Error:</b> Only image type can be uploaded</div>', 'bootbox');
                    }
                }
            };
            reader.readAsDataURL(file);
        });
        $(this).val("");
    });
</script>
