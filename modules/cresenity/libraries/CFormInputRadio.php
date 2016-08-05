<?php

    class CFormInputRadio extends CFormInput {

        protected $checked;
        protected $label;
        protected $applyjs;
        protected $label_wrap;
        protected $inline;

        public function __construct($id) {
            parent::__construct($id);

            $this->type = "radio";
            $this->label = "";
            $this->applyjs = "uniform";
            $this->checked = false;
            $this->inline = false;
            $this->label_wrap = false;
            $js_radio = carr::get($this->theme_data, 'js_radio');
            if (strlen($js_radio) > 0) {
                $this->applyjs = $js_radio;
            }
        }

        public static function factory($id) {
            return new CFormInputRadio($id);
        }

        public function set_applyjs($applyjs) {
            $this->applyjs = $applyjs;
            return $this;
        }

        public function set_checked($bool) {
            $this->checked = $bool;
            return $this;
        }

        public function set_label($label) {
            $this->label = $label;
            return $this;
        }

        public function set_label_wrap($bool) {
            $this->label_wrap = $bool;
            return $this;
        }

        function get_inline() {
            return $this->inline;
        }

        function set_inline($inline) {
            $this->inline = $inline;
            return $this;
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $disabled = "";
            $checked = "";
            if ($this->checked) $checked = ' checked="checked"';
            if ($this->disabled) $disabled = ' disabled="disabled"';

            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) $classes = " " . $classes;

            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }
            $label_class = 'radio-inline';
            if ($this->bootstrap == '3.3') {
                if ($this->radio >= '1.0') {
                    $label_class = 'control-label';
                }
            }
            $addition_attribute = "";
            foreach ($this->attr as $k => $v) {
                $addition_attribute.=" " . $k . '="' . $v . '"';
            }


            if ($this->bootstrap >= '3') {
                if ($this->inline == false) {
                    $html->append('<div class="radio ' . $classes . '" >');
                    $html->append(' <label>');
                }
                else {
                    $html->append('<label class="' . $label_class . ' ' . $classes . '" >');
                }
                $html->append('     <input type="radio" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $this->validation->validation_class() . '" '.$addition_attribute.' value="' . $this->value . '"' . $disabled . $checked . '>');
                if (strlen($this->label) > 0) {
                    if ($this->label_wrap) {
                        $html->appendln('<label for="' . $this->id . '" class="radio-label"><span></span>');
                    }
                    $html->appendln('&nbsp;' . $this->label);
                    if ($this->label_wrap) {
                        $html->appendln('</label>');
                    }
                }
                if ($this->inline == false) {
                    $html->append(' </label>');
                    $html->append('</div>');
                }
                else {
                    $html->append(' </label>');
                }
            }
            else {
                $html->append('<label class="checkbox' . $classes . '" >');
                if ($this->applyjs == "switch") {
                    $html->append('<div class="switch">');
                }

                $html->append('<input type="radio" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $this->validation->validation_class() . '" '.$addition_attribute.' value="' . $this->value . '"' . $disabled . $checked . '>');
                if (strlen($this->label) > 0) {
                    $html->appendln('&nbsp;' . $this->label);
                }
                if ($this->applyjs == "switch") {
                    $html->append('</div>');
                }
            }
            $html->append('</label>');
            $html->br();
            return $html->text();
        }

        public function js($indent = 0) {
            $js = new CStringBuilder();
            $js->set_indent($indent);
            $js->append(parent::js($indent))->br();
            if ($this->applyjs == "uniform") {
                //$js->append("$('#".$this->id."').uniform();")->br();
            }
            else if ($this->applyjs == "switch") {
                //$js->append("$('#".$this->id."').parent().bootstrapSwitch();")->br();
            }
            else if ($this->applyjs == 'icheck') {
                $js->append("
                    $('#" . $this->id . "').iCheck({
                        radioClass: 'iradio iradio_minimal-blue'
                    });
                ");
            }

            return $js->text();
        }

    }
    