<?php
class CMobile_Element_Control_Select extends CMobile_Element_AbstractControl {

    protected $group_list = array();
    protected $multiple;
    protected $applyjs;
    protected $prefix_icon;
    protected $list;

    public function __construct($id) {
        parent::__construct($id);
        $this->tag = "select";
        $this->multiple = false;
        $this->applyjs = "false";
        $this->prefix_icon = '';
        $this->list = array();
    }

    public static function factory($id) {
        return new CMobile_Element_Control_Select($id);
    }

    public function set_multiple($bool) {
        $this->multiple = $bool;
        return $this;
    }

    public function set_prefix_icon($prefix_icon){
        $this->prefix_icon = $prefix_icon; 
        return $this;
    }

    public function set_list($list) {
        $this->list = $list;
        return $this;
    }

    public function set_group_list($group_list) {
        $this->group_list = $group_list;
        return $this;
    }

    // public function set_applyjs($applyjs) {
    //     $this->applyjs = $applyjs;
    //     return $this;
    // }

    // public function set_lookup($query) {
        
    // }

    // public function add_group_list($group, $list) {
    //     $this->group_list[$group] = $list;
    //     return $this;
    // }

    // public function toarray() {
    //     $data = array();
    //     $data = array_merge_recursive($data, parent::toarray());
    //     if ($this->multiple) {
    //         $data["attr"]["multiple"] = "multiple";
    //     }
    //     $data["children"] = array();

        // if ($this->list != null) {
        //     foreach ($this->list as $k => $v) {
        //         $selected = "";
        //         if (is_array($this->value)) {
        //             if (in_array($k, $this->value))
        //                 $selected = ' selected="selected"';
        //         } else {
        //             if ($this->value == (string) $k)
        //                 $selected = ' selected="selected"';
        //         }
        //         $child = array();
        //         $child["tag"] = "option";
        //         $child["attr"]["value"] = $k;
        //         if (strlen($selected) > 0) {
        //             $child["attr"]["selected"] = 'selected';
        //         }
        //         $child["text"] = $v;
        //         $data["children"][] = $child;
        //     }
        // }
    //     return $data;
    // }

    protected function html_attr() {
        $html_attr = parent::html_attr();
        $multiple = "";
        
        if ($this->multiple)
            $multiple = 'multiple';
                
        $html_attr .= $multiple;
        return $html_attr;
    }

    public function build($indent = 0) {
        $this->add_class( $this->validation->validation_class());
        $html_attr = $this->html_attr();
        if (strlen($this->prefix_icon) > 0) {
            $this->before()->add_icon()->set_icon($this->prefix_icon)->set_type('prefix'); 
        }
        if ($this->list != null) {
            foreach ($this->list as $k => $v) {
                $this->add('<option value="' . $k . '">' . $v . '</option>');
            }
        }
        if ($this->group_list != null) {
            foreach ($this->group_list as $k_group => $list) {
                $this->add('<optgroup label="' . $k_group . '">');
                foreach ($list as $k_list => $v) {
                    $this->add('<option value="' . $k_list . '">' . $v . '</option>');
                }
                $this->add('</optgroup>');
            }
        }
    }

    // public function html($indent = 0) {
    //     $html = new CStringBuilder();
    //     $html->set_indent($indent);
    //     $disabled = "";
    //     if ($this->disabled)
    //         $disabled = ' disabled="disabled"';
    //     $multiple = "";
    //     if ($this->multiple)
    //         $multiple = ' multiple="multiple"';
    //     $name = $this->name;
    //     if ($this->multiple)
    //         $name = $name . "[]";
    //     $classes = $this->classes;
    //     $classes = implode(" ", $classes);
    //     if (strlen($classes) > 0)
    //         $classes = " " . $classes;
    //     if ($this->bootstrap == '3') {
    //         $classes = $classes ." form-control ";
    //     }
    //     $custom_css = $this->custom_css;
    //     $custom_css = crenderer::render_style($custom_css);
    //     if (strlen($custom_css) > 0) {
    //         $custom_css = ' style="' . $custom_css . '"';
    //     }
    //     $html->appendln('<select name="' . $name . '" id="' . $this->id . '" class="select' . $classes . $this->validation->validation_class() . '"' . $custom_css . $disabled . $multiple . '>')->inc_indent()->br();
    //     if (count($this->group_list) > 0) {
    //         foreach ($this->group_list as $g => $list) {
    //             if (strlen($g) > 0) {
    //                 $html->appendln('<optgroup label="' . $g . '">')->br();
    //             }
    //             foreach ($list as $k => $v) {
    //                 $selected = "";
    //                 if (is_array($this->value)) {
    //                     if (in_array($k, $this->value))
    //                         $selected = ' selected="selected"';
    //                 } else {
    //                     if ($this->value == (string) $k)
    //                         $selected = ' selected="selected"';
    //                 }
    //                 $html->appendln('<option value="' . $k . '"' . $selected . '>' . $v . '</option>')->br();
    //             }
    //             if (strlen($g) > 0) {
    //                 $html->appendln('</optgroup>')->br();
    //             }
    //         }
    //     }
    //     if ($this->list != null) {
    //         foreach ($this->list as $k => $v) {
    //             $selected = "";
    //             if (is_array($this->value)) {
    //                 if (in_array($k, $this->value))
    //                     $selected = ' selected="selected"';
    //             } else {
    //                 if ($this->value == (string) $k)
    //                     $selected = ' selected="selected"';
    //             }
    //             $html->appendln('<option value="' . $k . '"' . $selected . '>' . $v . '</option>')->br();
    //         }
    //     }
    //     $html->dec_indent()->appendln('</select>')->br();

    //     //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
    //     return $html->text();
    // }

    public function js($indent = 0) {
        $js = "$('#" . $this->id . "').material_select();";
    //     $js = new CStringBuilder();
    //     $js->set_indent($indent);
    //     $js->append(parent::js($indent))->br();
    //     if ($this->applyjs == "select2") {
    //         CManager::instance()->register_module('select2');
    //         $js->append("$('#" . $this->id . "').select2();")->br();
    //     }
    //     if ($this->applyjs == "chosen") {
    //         $js->append("$('#" . $this->id . "').chosen();")->br();
    //     }
    //     if ($this->applyjs == "dualselect") {
    //         $js->append("$('#" . $this->id . "').multiSelect();")->br();
    //     }

        return $js;
    }

}
