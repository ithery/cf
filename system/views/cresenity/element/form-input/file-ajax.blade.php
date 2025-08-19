<?php
defined('SYSPATH') or die('No direct access allowed.');

?>

<div id="container-{{ $id }}" class="fileupload fileupload-new" >
    <div class="fileupload-new">
        <i class="far fa-file fileupload-new"></i> <span class="fileupload-new">{{ $fileName }}</span>
    </div>
    <div class="fileupload-preview fileupload-exists">
        <i class="far fa-file fileupload-exists"></i> <span class="fileupload-exists"></span>
    </div>
    <div>
        @if(!$disabledUpload)
            <span class="btn btn-file btn-primary">
                <span class="fileupload-new">@lang('element/file.selectFile')</span>
                <span class="fileupload-change fileupload-exists">@lang('element/file.change')</span>
                <input id="input-temp-{{ $id }}" type="file" name="input-temp-{{ $id }}" style="display:none;" accept="{{ $acceptFile }}"/>
                <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="<?php echo $value; ?>" />
            </span>
            <a href="javascript:;" class="btn fileupload-remove fileupload-exists btn-danger" data-dismiss="fileupload">@lang('element/file.remove')</a>
        @endif
    </div>
</div>

<script>

    var <?php echo $id; ?>maxUploadSize = <?php echo $maxUploadSize; ?> * 1024 * 1024;

    $('#container-<?php echo $id; ?> span.fileupload-new, #container-<?php echo $id; ?> .btn-file span').click(function () {
        $('#input-temp-<?php echo $id; ?>').trigger('click');
    });

    $('#container-<?php echo $id; ?> .fileupload-remove').click(function () {
        $('#container-<?php echo $id; ?> .fileupload-preview span').html('');
        $("#<?php echo $id; ?>").val('');
        $("#<?php echo $id; ?>").trigger('change');
        $('#container-<?php echo $id; ?>').removeClass('fileupload-exists');
        $('#container-<?php echo $id; ?>').addClass('fileupload-new');
    });

    $('#input-temp-<?php echo $id; ?>').change(function (e) {
        $.each(e.target.files, function (i, file) {
            var reader = new FileReader();
            reader.fileName = file.name;
            reader.onload = function (event) {
                var filesize = event.total;
                var maxUploadSize = <?php echo $id; ?>maxUploadSize;
                if (maxUploadSize && filesize > maxUploadSize) {
                    cresenity.message('error', 'File Size is more than ' + <?php echo $maxUploadSize; ?> + ' MB');
                } else {
                    $('#container-<?php echo $id; ?> .fileupload-preview span').html(event.target.fileName);
                    $('#container-<?php echo $id; ?>').removeClass('fileupload-new');
                    $('#container-<?php echo $id; ?>').addClass('fileupload-exists');
                    $('#container-<?php echo $id; ?> .fileupload-preview').addClass('loading spinner');
                    $('#container-<?php echo $id; ?> .fileupload-preview').find('span').click(function () {
                        $('#input-temp-<?php echo $id; ?>').trigger('click');
                    });
                    var data = new FormData();
                    data.append('<?php echo $ajaxName; ?>[]', file);
                    data.append('<?php echo $ajaxName; ?>_filename[]', file.name);
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            cresenity.handleJsonResponse(response, function (dataFile) {
                                $('#<?php echo $id; ?>').val(dataFile.fileId);
                                $('#container-<?php echo $id; ?> .fileupload-preview span').html(dataFile.fileName);
                                $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('loading');
                                $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('spinner');
                                $('#<?php echo $id; ?>').trigger('change');

                            });
                        } else if (this.readyState == 4 && this.status != 200) {
                            cresenity.message('error', 'File Upload Failed');
                        }
                    };
                    xhr.open("post", '<?php echo $ajaxUrl; ?>');
                    xhr.send(data);
                }
            };
            reader.readAsDataURL(file);
        });
        $(this).val("");
    });
</script>
