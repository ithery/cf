<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 3:50:56 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_SelectTag extends CElement_FormInput {

    protected $multiple;

    public function __construct($id) {
        parent::__construct($id);
        $this->multiple = true;
    }

    public function set_multiple($bool) {
        $this->multiple = $bool;
        return $this;
    }

    public function set_min_input_length($min_input_length) {
        $this->min_input_length = $min_input_length;
        return $this;
    }

    public function set_key_field($key_field) {
        $this->key_field = $key_field;
        return $this;
    }

    public function set_search_field($search_field) {
        $this->search_field = $search_field;
        return $this;
    }

    public function set_query($query) {
        $this->query = $query;
        return $this;
    }

    public function set_format_result($fmt) {
        $this->format_result = $fmt;
        return $this;
    }

    public function set_format_selection($fmt) {
        $this->format_selection = $fmt;
        return $this;
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function html($indent = 0) {
        if (CManager::instance()->isRegisteredModule('bootstrap-4-material') || CManager::instance()->isRegisteredModule('bootstrap-4')) {
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $readonly = "";
            if ($this->readonly) {
                $readonly = ' readonly="readonly"';
            }
            $disabled = "";
            if ($this->disabled) {
                $disabled = ' disabled="disabled"';
            }
            $multiple = "";
            if ($this->multiple) {
                $multiple = ' multiple="multiple"';
            }
            $name = $this->name;
            if ($this->multiple)
                $name = $name . "[]";
            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0)
                $classes = " " . $classes;

            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }
            $addition_attribute = "";
            foreach ($this->attr as $k => $v) {
                $addition_attribute .= " " . $k . '="' . $v . '"';
            }
            $html->appendln('<select name="' . $name . '" id="' . $this->id . '" class="form-control select' . $classes . $this->validation->validation_class() . '"' . $custom_css . $disabled . $readonly . $multiple . $addition_attribute . '>')->inc_indent()->br();

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
                            $addition_attribute .= " " . $attribute_k . '="' . $attribute_v . '"';
                        }
                    }
                    if ($this->readonly) {
                        if ($k == $this->value) {
                            $html->appendln('<option value="' . $k . '" ' . $selected . $addition_attribute . '>' . $value . '</option>')->br();
                        }
                    } else {
                        $html->appendln('<option value="' . $k . '" ' . $selected . $addition_attribute . '>' . $value . '</option>')->br();
                    }
                }
            }
            $html->dec_indent()->appendln('</select>')->br();

            //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
            return $html->text();
        }
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $html->append($this->html_child($indent));

        $custom_css = crenderer::render_style($this->custom_css);
        $multiple = ' multiple="multiple"';
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $classes = implode(" ", $this->classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }
        if ($this->bootstrap == '3') {
            $classes = $classes . " form-control ";
        }

        $html->appendln('<input type="hidden"  class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" ' . $custom_css . '/>')->br();

        return $html->text();
    }

    public function js($indent = 0) {
        if (CManager::instance()->isRegisteredModule('bootstrap-4-material') || CManager::instance()->isRegisteredModule('bootstrap-4')) {
            $js = "
                $('#" . $this->id . "').select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                }).change(function() {

                });

            ";
            return $js;
        }
        $vals = $this->value;
        if (!is_array($vals)) {
            $vals = array($vals);
        }
        $vals_str = '';
        foreach ($vals as $val) {
            if (strlen($vals_str) > 0) {
                $vals_str .= ",";
            }
            $vals_str .= "'" . $val . "'";
        }

        $list = $this->list;
        if (!is_array($list)) {
            $list = array($list);
        }
        $list_str = '';
        foreach ($list as $val) {
            if (strlen($list_str) > 0) {
                $list_str .= ",";
            }
            $list_str .= "'" . $val . "'";
        }


        $str = "
			$('#" . $this->id . "').select2({
				tags: [" . $list_str . "],
				tokenSeparators: [',', ' ']
			}).change(function() {
				
			});
                        
	";

        if (strlen($vals_str) > 0) {
            $str .= "
                        $('#" . $this->id . "').select2('val',[" . $vals_str . "]);
                ";
        }

        $js = new CStringBuilder();
        $js->append(parent::js($indent))->br();
        $js->set_indent($indent);
        //echo $str;
        $js->append($str)->br();

        return $js->text();
    }

}
