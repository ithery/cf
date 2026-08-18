<?php
defined('SYSPATH') or die('No direct access allowed.');

?>

<div id="container-{{ $id }}" class="fileupload fileupload-new cres:element:control:FileAjax" cres-element="control:FileAjax" cres-config="{{ $cresConfig }}">
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
