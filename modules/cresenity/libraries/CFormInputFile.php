<?php

    class CFormInputFile extends CFormInput {

        protected $multiple;
        protected $applyjs;
        protected $paste_zone;
        protected $drop_zone;
        protected $url;
        protected $max_file_size;
        protected $resize;
        protected $accept_file_type;
        protected $callback_drop;
        protected $before_submit;
        protected $callback_success;
        protected $callback_progress;
        protected $auto_upload;
        protected $max_number_of_files;

        public function __construct($id) {
            parent::__construct($id);

            $this->multiple = false;
            $this->type = "file";
            $this->applyjs = "file-upload";
            $this->paste_zone = "jQuery('body')";
            $this->drop_zone = "jQuery('body')";
            $this->resize = true;
            $this->max_file_size = 99999;
            $this->accept_file_type = "/(\.|\/)(gif|jpe?g|png)$/i";
            $this->callback_drop = null;
            $this->before_submit = null;
            $this->callback_success = null;
            $this->callback_progress = null;
            $this->auto_upload = true;
            $this->max_number_of_files = null;

            $fileupload = carr::get($this->theme_data, 'fileupload');
            if (strlen($fileupload) > 0) {
                $this->applyjs = $fileupload;
            }
        }

        public static function factory($id) {
            return new CFormInputFile($id);
        }

        public function html($indent = 0) {
            if ($this->applyjs == 'jquery-fileupload') {
                CClientModules::instance()->register_module('jquery-fileupload');
            }
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $disabled = "";
            if ($this->disabled) $disabled = ' disabled="disabled"';
            $multiple = "";
            if ($this->multiple) $multiple = ' multiple="multiple"';
            $name = $this->name;
            if ($this->multiple) $name = $name . "[]";
            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) $classes = " " . $classes;
            if ($this->bootstrap == '3') {
                $classes = $classes . " form-control ";
            }
            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }

            $add_class = "fileupload-new";
            if (strlen($this->value) > 0) {
                $add_class = "fileupload-exists";
            }
            if ($this->applyjs == "file-upload") {
                $html->appendln('<div class="fileupload ' . $add_class . '" data-provides="fileupload">');
                $html->appendln('	<div class="input-group">');
                $html->appendln('		<div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview">' . $this->value . '</span></div>');
                $html->appendln('		<span class="btn btn-file"><span class="fileupload-new">' . clang::__('Select file') . '</span><span class="fileupload-exists">' . clang::__('Change') . '</span>');
            }
            $html->appendln('			<input type="file" name="' . $name . '" id="' . $this->id . '" class="file' . $classes . $this->validation->validation_class() . '"' . $custom_css . $disabled . $multiple . ' />')->inc_indent()->br();
            if ($this->applyjs == "file-upload") {
                $html->appendln('		</span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">' . clang::__('Remove') . '</a>');
                $html->appendln('	</div>');
                $html->appendln('</div>');
            }

            //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
            return $html->text();
        }

        public function js($indent = 0) {
            $js = new CStringBuilder();
            $js->set_indent($indent);
            if ($this->applyjs == "file-upload") {
                //$js->append("$('#".$this->id."').select2();")->br();
            }
            else if ($this->applyjs == 'jquery-fileupload') {
                $js->appendln("
                    jQuery('#" . $this->id . "').fileupload({
                        // Uncomment the following to send cross-domain cookies:
                        //xhrFields: {withCredentials: true},
                        url: '" . $this->url . "',
                ");
                if (!$this->auto_upload) {
                    $js->appendln("autoUpload: false,");
                }
                if (strlen($this->max_number_of_files) > 0) {
                    $js->appendln('maxNumberOfFiles: ' .$this->max_number_of_files .',');
                }
                
                if (strlen($this->callback_progress) > 0) {
                    $js->appendln("progress: function(e, data){
                                " . $this->callback_progress . "
                            },");
                }
                $js->appendln(" add: function (e, data) {
                            // this code below for create preview image using blob system
                            // URL.createObjectURL(data.files[0]);

                            " . $this->before_submit . "
                            if (data.autoUpload || (data.autoUpload !== false &&
                                        $(this).fileupload('option', 'autoUpload'))) {
                                    data.url = '" . $this->url . "';
                                    data.dataType = 'json';
                                    var jqXHR = data.submit()
                                        .success(function (result, textStatus, jqXHR) {
                                        " . $this->callback_success . "
                                        })
                                        .error(function (jqXHR, status, thrown) {
                                            console.log(jqXHR);
                                            if (status == 'error') {
                                                $.cresenity.message('error','Error, please call administrator... (' + thrown + ')');
                                            }
                                        })
//                                        .complete(function (result, textStatus, jqXHR) {
//                                        console.log('complete');
//                                        console.log(jqXHR);
//                                        console.log(textStatus);
//                                        })
                                        ;
                            }
                        },
                ");
                if (strlen($this->callback_drop) > 0) {
                    $js->appendln("
                        drop: function(e, data){
                            " . $this->callback_drop . "
                        },
                        ");
                }
                if (strlen($this->paste_zone) > 0) {
                    $js->append("pasteZone: " . $this->paste_zone . ",");
                }
                if (strlen($this->drop_zone) > 0) {
                    $js->append("dropZone: " . $this->drop_zone . ",");
                }
                if ($this->resize == true) {
                    $js->appendln("
                            // Enable image resizing, except for Android and Opera,
                            // which actually support image resizing, but fail to
                            // send Blob objects via XHR requests:
                            disableImageResize: /Android(?!.*Chrome)|Opera/
                                .test(window.navigator.userAgent),");
                }
                if ($this->max_file_size != null) {
                    $js->appendln("maxFileSize: " . $this->max_file_size . ",");
                }
                if ($this->accept_file_type != null) {
                    $js->appendln("acceptFileTypes: " . $this->accept_file_type . ",");
                }
                $js->append("});");
            }
            return $js->text();
        }

        public function set_multiple($bool) {
            $this->multiple = true;
            return $this;
        }

        public function set_applyjs($applyjs) {
            $this->applyjs = $applyjs;
            return $this;
        }

        public function set_lookup($query) {
            
        }

        function get_paste_zone() {
            return $this->paste_zone;
        }

        function get_url() {
            return $this->url;
        }

        function get_max_file_size() {
            return $this->max_file_size;
        }

        function get_resize() {
            return $this->resize;
        }

        function get_accept_file_type() {
            return $this->accept_file_type;
        }

        function set_paste_zone($paste_zone) {
            $this->paste_zone = $paste_zone;
            return $this;
        }

        function set_url($url) {
            $this->url = $url;
            return $this;
        }

        function set_max_file_size($max_file_size) {
            $this->max_file_size = $max_file_size;
            return $this;
        }

        function set_resize($resize) {
            $this->resize = $resize;
            return $this;
        }

        function set_accept_file_type($accept_file_type) {
            $this->accept_file_type = $accept_file_type;
            return $this;
        }

        function get_callback_drop() {
            return $this->callback_drop;
        }

        function set_callback_drop($callback_drop) {
            $this->callback_drop = $callback_drop;
            return $this;
        }

        function get_before_submit() {
            return $this->before_submit;
        }

        function get_callback_success() {
            return $this->callback_success;
        }

        function set_before_submit($before_submit) {
            $this->before_submit = $before_submit;
            return $this;
        }

        function set_callback_success($callback_success) {
            $this->callback_success = $callback_success;
            return $this;
        }

        function get_callback_progress() {
            return $this->callback_progress;
        }

        function set_callback_progress($callback_progress) {
            $this->callback_progress = $callback_progress;
            return $this;
        }
        public function get_drop_zone() {
            return $this->drop_zone;
        }

        public function get_auto_upload() {
            return $this->auto_upload;
        }

        public function set_drop_zone($drop_zone) {
            $this->drop_zone = $drop_zone;
            return $this;
        }

        public function set_auto_upload($auto_upload) {
            $this->auto_upload = $auto_upload;
            return $this;
        }

        public function get_max_number_of_files() {
            return $this->max_number_of_files;
        }

        public function set_max_number_of_files($max_number_of_files) {
            $this->max_number_of_files = $max_number_of_files;
            return $this;
        }

        
    }
    