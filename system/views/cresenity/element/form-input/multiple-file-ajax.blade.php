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

    .multi-image-ajax .div-img,  .multi-image-ajax .div-file{
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

<div id="container-{{ $id }}" class="container-multi-image-ajax" data-maximum="{{ $maximum }}" >
    <input id="{{ $id }}_input_temp" type="file" accept="<?php echo $accept;?>" name="{{ $id }}_input_temp[]" class="multi-image-ajax-input-temp"  style="display:none;">
    <div id="{{ $id }}_message" class="row alert alert-danger fade in multi-image-ajax-message">
    </div>
    <div id="{{ $id }}_description" class="multi-image-ajax-description">@lang('element/file.clickOrDropFilesOnBoxBelow')</div>
    <div id="{{ $id }}" class="row control-fileupload multi-image-ajax">
        @foreach($files as $f)
            @php
            $input_name = carr::get($f, 'input_name');
            $input_value = carr::get($f, 'input_value');
            $file_url = carr::get($f, 'file_url');
            $file_name = carr::get($f, 'file_name');
            $isImage = false;
            $pathParts = pathinfo($file_url);
            $ext = carr::get($pathParts,'extension');
            $imgExtensions = array("jpg", "jpeg", "png", "gif");

            if (in_array($ext, $imgExtensions)) {
                if (@getimagesize($file_url)) {
                    $isImage = true;
                }
            }
            @endphp
            <div class="multi-image-ajax-file container-file-upload">
                <div class="div-img">
                    @if($isImage)

                        <img src="{{ $file_url }}" />
                    @else
                        <div class="mime-icon ico-{{ $ext }}">
                            <div class="ico"></div>
                        </div>
                    @endif
                    <input type="hidden" name="{{ $name }}[{{ $input_name }}]" value="{{ $input_value }}">
                </div>
                @foreach ($customControl as $cc)
                    <?php
                    $control = carr::get($cc, 'control');
                    $controlName = carr::get($cc, 'input_name');
                    $controlLabel = carr::get($cc, 'input_label');
                    //get value
                    $control_value_array = carr::get($customControlValue, $input_name, []);
                    $control_value = carr::get($control_value_array, $controlName);
                    ?>
                    <div class="div-custom-control">
                        <label><?php echo $controlLabel; ?>:</label><input type="{{ $control }}" name="{{ $name }}_custom_control[{{ $input_name }}][{{ $controlName }}]" value="{{ $control_value }}"  >
                    </div>
                @endforeach
                <p class="multi-image-ajax-filename">{{ $file_name }}</p>
                <a class="multi-image-ajax-remove">@lang('element/file.remove')</a>
            </div>
        @endforeach
    </div>
    <div>
        <div class="multi-image-ajax-btn-upload btn btn-success">@lang('element/file.uploadFile')</div>
    </div>

</div>
<script>

    (function ($) {
        $(function () {
            const asFileAjax = {{ $asFileAjax  ? 'true' : 'false'}};
            const errorMessageLimitFile = '@lang("element/file.errorMessageLimitFile",["limit" => $limitFile])';
            const errorMessageMaxUploadSize = '@lang("element/file.errorMessageMaxUploadSize",["sizeMB" => $maxUploadSize])';
            const removeLabel = "@lang('element/file.remove')";
            const elementId = "{{ $id }}";
            var haveCropper = {{ ($cropper != null) ? 'true' : 'false'  }};
            const maxUploadSize = {{  $maxUploadSize  }} * 1024 * 1024;
            const readerOnLoadResolver = (child, file, reader) =>{
                return $.proxy(function (file, fileList, event) {
                    var filesize = event.total;
                    var limitFile = <?= $limitFile ?>;
                    if (limitFile && $("#" + elementId).children().length >= limitFile) {
                        cresenity.showError(errorMessageLimitFile);
                    } else {
                        if (maxUploadSize && filesize > maxUploadSize) {
                            cresenity.showError(errorMessageMaxUploadSize);
                        } else {
                            insertFile(reader, file, fileList, event);
                        }
                    }
                }, child, file, $("#" + elementId));
            };

            @if($cropper != null)
                var cropperWidth = parseFloat('<?php echo $cropper->getCropperWidth(); ?>');
                var cropperHeight = parseFloat('<?php echo $cropper->getCropperHeight(); ?>');
                var cropBoxResizable = <?php echo json_encode($cropper->getCropperResizable()); ?>;
            @endif
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
                $("#" + elementId).trigger('change');
            }
            fileChanged();
            // Remove File
            function fileUploadRemove(e) {

                $('#container-' + elementId + ' .multi-image-ajax-remove').click(function (e) {
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
                const name = file.name;
                const lastDot = name.lastIndexOf('.');

                const fileName = name.substring(0, lastDot);
                const ext = name.substring(lastDot + 1);

                var img = file.type.match("image.*") ? $("<img src=" + imageData + " /> ") : $('<div>').addClass("mime-icon ico-" + ext).append($('<div>').addClass('ico'));
                var div = $("<div>").addClass("multi-image-ajax-file container-file-upload");
                div.click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
                var div_img = $("<div>").addClass("div-file");

                div_img.append(img);
                div.append(div_img);

                var div_cc;
                var cc_label;
                var cc;
                @foreach ($customControl as $cc)
                    @php
                    $control = carr::get($cc, 'control');
                    $controlName = carr::get($cc, 'input_name');
                    $controlLabel = carr::get($cc, 'input_label');
                    @endphp
                    div_cc = $("<div>").addClass("div-custom-control");
                    cc_label = $("<label>").html("{{ $controlLabel }} :");
                    cc = $("<input type=\"{{ $control }}\" name=\"{{ $name }}_custom_control[" + index + "][{{  $controlName }}]\">");
                    div_cc.append(cc_label);
                    div_cc.append(cc);
                    div.append(div_cc);
                @endforeach
                var filenameLabel = $("<p>").addClass("multi-image-ajax-filename").html(name);
                div.append(filenameLabel);
                @if($removeLink)
                    var remove = $("<a>").addClass("multi-image-ajax-remove").html("@lang('element/file.remove')");
                    div.append(remove);
                @endif
                div.append("<img class=\"multi-image-ajax-loading\" src=\"<?php echo curl::base(); ?>media/img/ring.gif\" />");
                fileList.append(div.addClass("loading"));
                fileUploadRemove();
                var data = new FormData();
                data.append("{{ $name }}[]", imageData);

                data.append('{{ $ajaxName }}_filename[]', file.name);
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        cresenity.handleJsonResponse(response, function (dataFile) {
                            div.removeClass("loading");
                            if(asFileAjax) {
                                div.append("<input type=\"hidden\" name=\"<?php echo $name; ?>[]\" value=" + JSON.stringify(dataFile) + ">");
                            } else {
                                div.append("<input type=\"hidden\" name=\"<?php echo $name; ?>[]\" value=" + dataFile.fileId + ">");
                            }

                            if(file.type.match("image.*")){
                                img.attr('src', data.url);
                            }
                            index++;
                            fileChanged();
                        });
                    } else if (this.readyState == 4 && this.status != 200) {
                        cresenity.message('error', 'File Upload Failed');
                        //div.remove();
                    }
                };
                xhr.open("post", "{{ $ajaxUrl }}");
                xhr.send(data);
            }

            fileUploadRemove();

            $("#" + elementId).sortable();
            $("#" + elementId).on({
                "dragover dragenter": function (e) {
                    $(this).addClass("ondrag");
                },
                "dragleave dragend": function (e) {
                    $(this).removeClass("ondrag");
                },
                "drop": function (e) {
                    $(this).removeClass("ondrag");
                    var container = $('#container-' + elementId);
                    if (!container.hasClass('disabled')) {
                        $("#"+elementId).sortable();
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
                            $("#container-" + elementId +" .multi-image-ajax-description").remove();
                            $.each(dataTransferFiles, function (i, file) {
                                var reader = new FileReader();
                                reader.fileName = file.name;

                                reader.onload = readerOnLoadResolver(this,file,reader);
                                reader.readAsDataURL(file);
                            });
                        }
                    }
                }
            })
            if (!haveCropper) {
                $("#" + elementId + "_input_temp").attr('multiple', 'multiple');
            }

            // Add Image by Click
            $("#" + elementId).click(function () {
                var container = $('#container-<?php echo $id ?>');
                if (!container.hasClass('disabled')) {
                    $("#<?php echo $id; ?>_input_temp").trigger("click");
                }
            })
            $("#" + elementId +"_input_temp").change(function (e) {
                $("#" + elementId + "_description").remove();
                $.each(e.target.files, function (i, file) {

                    var reader = new FileReader();

                    reader.fileName = file.name;
                    reader.onload = readerOnLoadResolver(this,file,reader);
                    reader.readAsDataURL(file);
                })
                $(this).val("");
            })

        }); // end of document ready
    })(jQuery); // end of jQuery name space

</script>
