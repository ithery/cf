<?php

    class CMobile_Element_FormInput_Textarea extends CMobile_Element_AbstractFormInput {

        protected $col;
        protected $row;
        protected $placeholder;
        protected $label_float;

        public function __construct($id) {
            parent::__construct($id);

            $this->type = "textarea";
            $this->placeholder = "";
            $this->label_float = true;
            $this->col = 60;
            $this->row = 10;
        }

        public static function factory($id) {
            return new CMobile_Element_FormInput_Textarea($id);
        }

        public function set_col($col) {
            $this->col = $col;
            return $this;
        }

        public function set_row($row) {
            $this->row = $row;
            return $this;
        }

        protected function html_attr() {
            $html_attr = parent::html_attr();
            $placeholder = "";
            $cols = "";
            $rows = "";
            
            if ($this->placeholder)
                $placeholder = ' placeholder="'.$this->placeholder.'"';
            if ($this->col)
                $cols = ' cols="'.$this->col.'"';
            if ($this->row)
                $rows = ' cols="'.$this->row.'"';
                    
            $html_attr .= $cols;
            $html_attr .= $rows;
            return $html_attr;
        }
        public function html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);

            $this->add_class('input-unstyled');
            $this->add_class('form-control');
            $this->add_class( $this->validation->validation_class());
            $html_attr = $this->html_attr();

            $html->appendln('<textarea name="' . $this->name . '" id="' . $this->id . '>' . $this->value . '</textarea>')->br();
            // $html->appendln('<p class="help-block">' . $this->placeholder . '</p>')->br();
            //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
            return $html->text();
        }

        public function js($indent = 0) {
            return "";
        }

    }