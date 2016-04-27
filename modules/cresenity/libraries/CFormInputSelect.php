<?php

    class CFormInputSelect extends CFormInput {

        protected $group_list = array();
        protected $multiple;
        protected $applyjs;
        protected $dropdown_classes;
        protected $hide_search;

        public function __construct($id) {
            parent::__construct($id);
            
            $this->dropdown_classes = array();
            $this->tag = "select";
            $this->multiple = false;
            $this->type = "select";
            $this->applyjs = "false";
            $this->hide_search = false;
        }

        public static function factory($id) {
            return new CFormInputSelect($id);
        }

        public function set_multiple($bool) {
            $this->multiple = $bool;
            return $this;
        }

        public function set_applyjs($applyjs) {
            $this->applyjs = $applyjs;
            return $this;
        }

        public function set_lookup($query) {
            
        }

        public function add_group_list($group, $list) {
            $this->group_list[$group] = $list;
            return $this;
        }
        
        public function add_dropdown_class($c) {
            if (is_array($c)) {
                $this->dropdown_classes = array_merge($c, $this->dropdown_classes);
            }
            else {
                if ($this->bootstrap == '3.3') {
                    $c = str_replace('span', 'col-md-', $c);
                    $c = str_replace('row-fluid', 'row', $c);
                }
                $this->dropdown_classes[] = $c;
            }
            return $this;
        }

        public function toarray() {
            $data = array();
            $data = array_merge_recursive($data, parent::toarray());
            if ($this->multiple) {
                $data["attr"]["multiple"] = "multiple";
            }
            $data["children"] = array();

            if ($this->list != null) {
                foreach ($this->list as $k => $v) {
                    $selected = "";
                    if (is_array($this->value)) {
                        if (in_array($k, $this->value))
                                $selected = ' selected="selected"';
                    } else {
                        if ($this->value == (string) $k)
                                $selected = ' selected="selected"';
                    }
                    $child = array();
                    $child["tag"] = "option";
                    $child["attr"]["value"] = $k;
                    if (strlen($selected) > 0) {
                        $child["attr"]["selected"] = 'selected';
                    }
                    $child["text"] = $v;
                    $data["children"][] = $child;
                }
            }
            return $data;
        }

        public function html($indent = 0) {
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
            if ($this->bootstrap >= '3') {
                $classes = $classes . " form-control ";
            }
            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }
            $addition_attribute = "";
            foreach ($this->attr as $k => $v) {
                $addition_attribute.=" " . $k . '="' . $v . '"';
            }
            $html->appendln('<select name="' . $name . '" id="' . $this->id . '" class="select' . $classes . $this->validation->validation_class() . '"' . $custom_css . $disabled . $multiple . $addition_attribute. '>')->inc_indent()->br();
            if (count($this->group_list) > 0) {
                foreach ($this->group_list as $g => $list) {
                    if (strlen($g) > 0) {
                        $html->appendln('<optgroup label="' . $g . '">')->br();
                    }
                    foreach ($list as $k => $v) {
                        $selected = "";
                        if (is_array($this->value)) {
                            if (in_array($k, $this->value))
                                    $selected = ' selected="selected"';
                        } else {
                            if ($this->value == (string) $k)
                                    $selected = ' selected="selected"';
                        }
                        $html->appendln('<option value="' . $k . '"' . $selected . '>' . $v . '</option>')->br();
                    }
                    if (strlen($g) > 0) {
                        $html->appendln('</optgroup>')->br();
                    }
                }
            }
            if ($this->list != null) {
                foreach ($this->list as $k => $v) {
                    $selected = "";
                    if (is_array($this->value)) {
                        if (in_array($k, $this->value))
                                $selected = ' selected="selected"';
                    } else {
                        if ($this->value == (string) $k)
                                $selected = ' selected="selected"';
                    }
                    $value = $v;
                    $addition_attribute = ' ';
                    if (is_array($v)) {
                        $value = carr::get($v, 'value');
                        $attributes = carr::get($v, 'attributes', array());
                        foreach ($attributes as $attribute_k => $attribute_v) {
                            $addition_attribute.=" " . $attribute_k . '="' . $attribute_v . '"';
                        }
                    }
                    $html->appendln('<option value="' . $k . '" ' . $selected . $addition_attribute . '>' . $value . '</option>')->br();
                }
            }
            $html->dec_indent()->appendln('</select>')->br();

            //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
            return $html->text();
        }

        public function js($indent = 0) {

            $js = new CStringBuilder();
            $js->set_indent($indent);
            $js->append(parent::js($indent))->br();
            if ($this->applyjs == "select2") {
                if ($this->select2 == '4') {
                    CManager::instance()->register_module('select2-4.0');
                }
                else {
                    CManager::instance()->register_module('select2');
                }
                $classes = $this->classes;
                $classes = implode(" ", $classes);
                if (strlen($classes) > 0) {
                    $classes = " " . $classes;
                }
                if ($this->bootstrap == '3.3') {
                    $classes = $classes . " form-control ";
                }
                $dropdown_classes = $this->dropdown_classes;
                $dropdown_classes = implode(" ", $dropdown_classes);
                if (strlen($dropdown_classes) > 0) {
                    $dropdown_classes = " " . $dropdown_classes;
                }
                $js->append("$('#" . $this->id . "').select2({
                        dropdownCssClass: '" .$dropdown_classes ."', // apply css that makes the dropdown taller
                        containerCssClass : 'tpx-select2-container " . $classes . "',
                        ");
                if ($this->hide_search) {
                    $js->append("minimumResultsForSearch: Infinity,");
                }
                $js->append("});")->br();
            }
            if ($this->applyjs == "chosen") {
                $js->append("$('#" . $this->id . "').chosen();")->br();
            }
            if ($this->applyjs == "dualselect") {
                $js->append("$('#" . $this->id . "').multiSelect();")->br();
            }

            return $js->text();
        }
        
        public function get_hide_search() {
            return $this->hide_search;
        }

        public function set_hide_search($hide_search) {
            $this->hide_search = $hide_search;
            return $this;
        }
    
    }
    