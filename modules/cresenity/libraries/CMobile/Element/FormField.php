<?php

    class CMobile_Element_FormField extends CMobile_Element {

        protected $group_classes = array();
        protected $group_id = "";
        protected $group_custom_css = array();
        protected $label = array();
        protected $show_label = array();
        protected $label_size = array();
        protected $fullwidth = array();
        protected $info_text = array();
        protected $label_class = array();
        protected $style_form_group;
        protected $label_float;
        protected $data_error;
        protected $data_success;


        public function __construct($id = "") {
            parent::__construct($id);
            $this->tag = "div";
            $this->label = "";
            $this->show_label = true;
            $this->label_size = "medium";
            $this->info_text = "";
            $this->fullwidth = false;
            $this->group_id = "";
            $this->group_classes = array();
            $this->group_custom_css = array();
            $this->style_form_group = null;
            $this->label_float = true;
        }

        public static function factory($id = "") {
            return new CMobile_Element_FormField($id);
        }

        public function set_group_id($id) {
            $this->group_id = $id;
            return $this;
        }

        public function add_group_class($class) {
            $this->group_classes[] = $class;
            return $this;
        }

        public function group_custom_css($key, $val) {
            $this->group_custom_css[$key] = $val;
            return $this;
        }

        public function set_data_error($data_error){
            $this->data_error = $data_error; 
            return $this;
        }

        public function set_data_success($data_success){
            $this->data_success = $data_success; 
            return $this;
        }

        // public function set_label_size($size) {
        //     if (in_array($size, array("small", "medium", "large"))) {
        //         $this->label_size = $size;
        //     }
        //     return $this;
        // }

        public function set_label_size($size) {
            if (in_array($size, array("small", "medium", "large"))) {
                $this->label_size = $size;
            }
            
            return $this;
        }

        public function set_info_text($info_text) {
            $this->info_text = $info_text;
            return $this;
        }

        public function set_label($text, $lang = true) {
            if ($lang) $text = clang::__($text);
            $this->label = $text;
            return $this;
        }

        public function show_label() {
            $this->show_label = true;
            return $this;
        }

        public function hide_label() {
            $this->show_label = false;
            return $this;
        }
        public function style_form_inline() {
            $this->style_form_group = "inline";
            return $this;
        }

        public function add_label_class($label_class) {
            $this->label_class[] = $label_class;
            return $this;
        }

        public function disable_label_float() {
            $this->label_float = false;
            return $this;
        }

        public function toarray() {
            $data = array();

            $control_data = array_merge_recursive($data, parent::toarray());
            $data['attr']['class'] = "control-group";
            $control_label = array();
            $control_label['tag'] = 'label';
            $control_label['attr']['class'] = 'control-label';
            $control_label['attr']['id'] = $this->id . '-label';
            $control_label['text'] = $this->label;

            $control_wrapper = array();
            if (isset($control_data['children'])) {
                $control_wrapper['children'] = $control_data['children'];
            }
            $control_wrapper['tag'] = "div";

            $data['children'][] = $control_label;
            $data['children'][] = $control_wrapper;
            $data['tag'] = $this->tag;
            return $data;
        }



        public function html($indent = 0) {
            $this->add_class('input-field');
            
            if ($this->show_label) {
                $this->add_label()->set_label($this->label)->set_for_id($this->id)->set_data_error($this->data_error)->set_data_success($this->data_success);
                //$html->appendln('<label id="' . $this->id . '" class="' . $label_class . '">' . $this->label . '</label>')->br();
            }
            
            return parent::html($indent);
        }

        public function set_style_form_group($style_form_group) {
            $this->style_form_group = $style_form_group;
            return $this;
        }

        public function js($indent = 0) {
            $js = CStringBuilder::factory()->set_indent($indent);

            $js->set_indent($indent);

            $js->appendln(parent::js($js->get_indent()))->br();

            return $js->text();
        }

    }

?>