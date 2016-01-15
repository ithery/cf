<?php

    class CFormField extends CElement {

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
        }

        public static function factory($id = "") {
            return new CFormField($id);
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

        // public function set_label_size($size) {
        //     if (in_array($size, array("small", "medium", "large"))) {
        //         $this->label_size = $size;
        //     }
        //     return $this;
        // }

        public function set_label_size($size) {
            // if (in_array($size, array("small", "medium", "large"))) {
                $this->label_size = $size;
            // }
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

            $html = new CStringBuilder();
            $html->set_indent($indent);

            $group_classes = $this->group_classes;
            $group_classes = implode(" ", $group_classes);
            if (strlen($group_classes) > 0)
            $group_classes = " " . $group_classes;
            $group_custom_css = $this->group_custom_css;
            $group_custom_css = crenderer::render_style($group_custom_css);
            if (strlen($group_custom_css) > 0) {
                $group_custom_css = ' style="' . $group_custom_css . '"';
            }
            $group_attr = "";
            if (strlen($this->group_id) > 0) {
                $group_attr.=' id="' . $this->group_id . '"';
            }

            $class_form_field = 'control-group';
            $label_class = '';
            if ($this->bootstrap == '3') {
                $class_form_field = 'form-group';
                if ($this->style_form_group == 'inline') {
                    $class_form_field .= ' row';
                    $label_class = 'col-md-' . $this->label_size;
                    $control_size = 12 - $this->label_size;
                    $control_class = 'col-md-' . $control_size;
                    if($this->label_size > 11) {
                        $control_class = 'col-md-' . 10;
                        $label_class = 'col-md-' . 2;
                    }
                    // $label_class = 'col-md-3';
                    // $control_class = 'col-md-9';
                    // if ($this->label_size == 'large') {
                    //     $label_class = 'col-md-5';
                    //     $control_class = 'col-md-7';
                    // } else if ($this->label_size == 'small') {
                    //     $label_class = 'col-md-1';
                    //     $control_class = 'col-md-11';
                    // } else {
                    //     $label_class = 'col-md-3';
                    //     $control_class = 'col-md-9';
                    // }
                }
            }
            $label_class .= ' ' .implode(' ', $this->label_class);
            $html->appendln('<div class="' . $class_form_field . ' ' . $group_classes . '" ' . $group_custom_css . $group_attr . '>')->inc_indent();
            if ($this->show_label) {
                $html->appendln('<label id="' . $this->id . '" class="' . $label_class . ' control-label">' . $this->label . '</label>')->br();
            }
            $fullwidth = "";
            if ($this->fullwidth) {
                $fullwidth.=" " . "full-width";
            }

            if ($this->bootstrap == '3') {
                if ($this->style_form_group == 'inline') {
                    $html->appendln('<div class="' . $control_class . '">')->inc_indent()->br();
                }
            }
            else {
                $html->appendln('<div class="controls">')->inc_indent()->br();
            }


            $html->appendln(parent::html($html->get_indent()))->br();
            if (strlen($this->info_text) > 0) {
                $html->appendln('<p class="help-block">' . $this->info_text . '</p>')->inc_indent()->br();
            }
            if ($this->bootstrap == '3') {
                if ($this->style_form_group == 'inline') {
                    $html->dec_indent()->appendln('</div>')->inc_indent()->br();
                }
            }
            else {
                $html->dec_indent()->appendln('</div>')->inc_indent()->br();
            }
            $html->dec_indent()->appendln('</div>');
            /*
              if(isset($field["info_bubble"])) {
              $html.='<span class="info-spot">';
              $html.='<span class="icon-info-round"></span>';
              $html.='<span class="info-bubble">';
              $html.=$field["info_bubble"];
              $html.='</span>';
              $html.='</span>';
              }
             */



            return $html->text();
        }

        public function set_style_form_group($style_form_group) {
            $this->style_form_group = $style_form_group;
            return $this;
        }

        public
                function js($indent = 0) {
            $js = CStringBuilder::factory()->set_indent($indent);

            $js->set_indent($indent);

            $js->appendln(parent::js($js->get_indent()))->br();

            return $js->text();
        }

    }

?>