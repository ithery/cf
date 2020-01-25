<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Checkbox
 *
 * @author Alvin
 * @since Mar 14, 2018, 15:35:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Checkbox extends CElement_FormInput {

    use CTrait_Compat_Element_FormInput_Checkbox,
        CTrait_Element_Property_Label;


    protected $checked = "";
    protected $applyjs = "";
    protected $display_inline = "";
    protected $label_wrap;
    protected $style = '';

    public function __construct($id) {
        parent::__construct($id);


        $this->style = 'minimal';
        $this->type = "checkbox";
        $this->label = "";
        $this->applyjs = "uniform";
        $this->checked = false;
        $this->display_inline = false;
        $this->label_wrap = false;
        $js_checkbox = carr::get($this->theme_data, 'js_checkbox');
        if (strlen($js_checkbox) > 0) {
            $this->applyjs = $js_checkbox;
        }
    }

    public static function factory($id) {
        return new CElement_FormInput_Checkbox($id);
    }

    public function set_applyjs($applyjs) {
        $this->applyjs = $applyjs;
        return $this;
    }

    public function setChecked($bool) {
        $this->checked = $bool;
        return $this;
    }

    public function set_label_wrap($bool) {
        $this->label_wrap = $bool;
        return $this;
    }

    public function set_display_inline($bool) {
        $this->display_inline = $bool;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        $checked = "";
        if ($this->checked)
            $checked = ' checked="checked"';
        if ($this->disabled)
            $disabled = ' disabled="disabled"';

        $label_addition_attr = '';
        if ($this->display_inline) {
            $label_addition_attr = 'style="display:inline-block;padding-right:5px"';
        }
        $label_class = "checkbox";
        if ($this->bootstrap == '3.3') {
            if ($this->checkbox >= '1.0') {
                $label_class = 'control-label';
            }
        }
        $html->append('<label class="' . $label_class . '" ' . $label_addition_attr . '>');
        if ($this->applyjs == "switch") {
            $html->append('<div class="switch">');
        }
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        if ($this->bootstrap == '3') {
            //$classes = $classes ." form-control ";
        }
        if ($this->bootstrap == '3.3') {
            if ($this->checkbox >= '1.0') {
                $classes = $classes . ' ' . $this->style . ' ';
            }
        }
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= " " . $k . '="' . $v . '"';
        }
        $html->append('<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled ' . $classes . '' . $this->validation->validation_class() . '" value="' . $this->value . '"' . $disabled . $custom_css . $checked . $addition_attribute . '>');
        //$html->append('<span></span>');
        if (strlen($this->label) > 0) {
            if ($this->label_wrap) {
                $html->appendln('<label for="' . $this->id . '" class="checkbox-label"><span></span>');
            }
            $html->appendln('&nbsp;' . $this->label);
            if ($this->label_wrap) {
                $html->appendln('</label>');
            }
        }
        if ($this->applyjs == "switch") {
            $html->append('</div>');
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
        } else if ($this->applyjs == "switch") {
            //$js->append("$('#".$this->id."').parent().bootstrapSwitch();")->br();
        } else if ($this->applyjs == 'icheck') {
            $js->append("
                    $('#" . $this->id . "').iCheck({
                        checkboxClass: 'icheckbox icheckbox_minimal-blue',
                    });
                ");
        }


        return $js->text();
    }


}
