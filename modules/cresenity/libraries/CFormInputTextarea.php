<?php

    class CFormInputTextarea extends CFormInput {

        protected $col;
        protected $row;

        public function __construct($id) {
            parent::__construct($id);

            $this->type = "textarea";
            $this->col = 60;
            $this->row = 10;
        }

        public static function factory($id) {
            return new CFormInputTextarea($id);
        }

        public function set_col($col) {
            $this->col = $col;
            return $this;
        }

        public function set_row($row) {
            $this->row = $row;
            return $this;
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $disabled = "";
            if ($this->disabled) $disabled = ' disabled="disabled"';
            if ($this->readonly) $disabled = ' readonly="readonly"';
            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) $classes = " " . $classes;
            if ($this->bootstrap >= '3') {
                $classes = $classes ." form-control ";
            }
            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }
            $html->appendln('<textarea cols="' . $this->col . '" rows="' . $this->row . '" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $classes . $this->validation->validation_class() . '" ' . $disabled . $custom_css . '>' . $this->value . '</textarea>')->br();
            //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
            return $html->text();
        }

        public function js($indent = 0) {
            return "";
        }

    }