<?php
defined('SYSPATH') or die('No direct access allowed.');

?>

<div id="container-<?php echo $id; ?>" class="fileupload fileupload-new" >
    <div class="fileupload-new">
        <i class="far fa-file fileupload-new"></i> <span class="fileupload-new"><?php echo $fileName; ?></span>
    </div>
    <div class="fileupload-preview fileupload-exists">
        <i class="far fa-file fileupload-exists"></i> <span class="fileupload-exists"></span>
    </div>
    <div>
        <?php if (!$disabledUpload): ?>
            <span class="btn btn-file btn-primary">
                <span class="fileupload-new"><?php echo clang::__('Select File'); ?></span>
                <span class="fileupload-change fileupload-exists"><?php echo clang::__('Change'); ?></span>
                <input id="input-temp-<?php echo $id; ?>" type="file" name="input-temp-<?php echo $id; ?>" style="display:none;" accept="<?php echo $acceptFile; ?>"/>
                <input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
            </span>
            <a href="javascript:;" class="btn fileupload-remove fileupload-exists btn-danger" data-dismiss="fileupload"><?php echo clang::__('Remove'); ?></a>
        <?php endif; ?>
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
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            var dataFile = JSON.parse(this.responseText);
                            $('#<?php echo $id; ?>').val(dataFile.fileId);
                            $('#container-<?php echo $id; ?> .fileupload-preview span').html(dataFile.fileName);
                            $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('loading');
                            $('#container-<?php echo $id; ?> .fileupload-preview').removeClass('spinner');
                            $('#<?php echo $id; ?>').trigger('change');
                        } else if (this.readyState == 4 && this.status != 200) {
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
