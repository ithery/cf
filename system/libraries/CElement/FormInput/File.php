<?php

class CElement_FormInput_File extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_File;

    protected $multiple;

    protected $applyjs;

    public function __construct($id) {
        parent::__construct($id);

        $this->multiple = false;
        $this->type = 'file';
        $this->applyjs = 'file-upload';

        $this->input_help = '';

        $this->applyjs = c::theme('fileupload', 'file-upload');
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    public function html($indent = 0) {
        if ($this->applyjs == 'jquery-fileupload') {
            c::manager()->registerModule('jquery-fileupload');
        }
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $disabled = '';
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }
        $multiple = '';
        if ($this->multiple) {
            $multiple = ' multiple="multiple"';
        }
        $name = $this->name;
        if ($this->multiple) {
            $name = $name . '[]';
        }
        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }

        $custom_css = $this->custom_css;
        $custom_css = $this->renderStyle($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $add_class = 'fileupload-new';
        if (strlen($this->value) > 0) {
            $add_class = 'fileupload-exists';
        }
        if ($this->applyjs == 'file-upload') {
            $html->appendln('<div class="fileupload ' . $add_class . '" data-provides="fileupload">');
            $html->appendln('	<div class="input-group">');
            $html->appendln('		<div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview">' . $this->value . '</span></div>');
            $html->appendln('		<span class="btn btn-file"><span class="fileupload-new">' . c::__('Select file') . '</span><span class="fileupload-exists">' . c::__('Change') . '</span>');
        }
        $html->appendln('			<input type="file" name="' . $name . '" id="' . $this->id . '" class="file' . $classes . $this->validation->validationClass() . '"' . $custom_css . $disabled . $multiple . ' />')->incIndent()->br();
        if ($this->applyjs == 'file-upload') {
            $html->appendln('		</span><a href="#" class="btn remove fileupload-exists" data-dismiss="fileupload">' . c::__('Remove') . '</a>');
            $html->appendln('	</div>');
            $html->appendln('</div>');
        }

        if (strlen($this->input_help) > 0) {
            $html->appendln('<span class="help-block">' . $this->input_help . '</span>');
        }

        //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        if ($this->applyjs == 'file-upload') {
            //$js->append("$('#".$this->id."').select2();")->br();
        } elseif ($this->applyjs == 'jquery-fileupload') {
            $js->appendln("
                jQuery('#" . $this->id . "').fileupload({
                    // Uncomment the following to send cross-domain cookies:
                    //xhrFields: {withCredentials: true},
                    url: '" . $this->url . "',
            ");
            if (!$this->auto_upload) {
                $js->appendln('autoUpload: false,');
            }
            if ($this->max_number_of_files !== null && strlen($this->max_number_of_files) > 0) {
                $js->appendln('maxNumberOfFiles: ' . $this->max_number_of_files . ',');
            }

            if ($this->callback_progress !== null && strlen($this->callback_progress) > 0) {
                $js->appendln('progress: function(e, data){
                    ' . $this->callback_progress . '
                },');
            }
            $js->appendln(' add: function (e, data) {
                // this code below for create preview image using blob system
                // URL.createObjectURL(data.files[0]);

                ' . $this->before_submit . "
                if (data.autoUpload || (data.autoUpload !== false &&
                    $(this).fileupload('option', 'autoUpload'))
                ) {
                    data.url = '" . $this->url . "';
                    data.dataType = 'json';
                    var jqXHR = data.submit()
                        .success(function (result, textStatus, jqXHR) {
                            " . $this->callback_success . "
                        })
                        .error(function (jqXHR, status, thrown) {
                            if (status == 'error') {
                                $.cresenity.message('error','Error, please call administrator... (' + thrown + ')');
                            }
                        });
                    }
                },
            ");
            if ($this->callback_drop != null && strlen($this->callback_drop) > 0) {
                $js->appendln('
                    drop: function(e, data){
                        ' . $this->callback_drop . '
                    },
                    ');
            }
            if (strlen($this->paste_zone) > 0) {
                $js->append('pasteZone: ' . $this->paste_zone . ',');
            }
            if (strlen($this->drop_zone) > 0) {
                $js->append('dropZone: ' . $this->drop_zone . ',');
            }
            if ($this->resize == true) {
                $js->appendln('
                        // Enable image resizing, except for Android and Opera,
                        // which actually support image resizing, but fail to
                        // send Blob objects via XHR requests:
                        disableImageResize: /Android(?!.*Chrome)|Opera/
                            .test(window.navigator.userAgent),');
            }
            if ($this->max_file_size != null) {
                $js->appendln('maxFileSize: ' . $this->max_file_size . ',');
            }
            if ($this->accept_file_type != null) {
                $js->appendln('acceptFileTypes: ' . $this->accept_file_type . ',');
            }
            $js->append('});');
        }

        return $js->text();
    }

    public function setMultiple($bool) {
        $this->multiple = true;

        return $this;
    }

    public function setApplyJs($applyjs) {
        $this->applyjs = $applyjs;

        return $this;
    }
}
