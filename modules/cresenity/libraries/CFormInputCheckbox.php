<?php

class CFormInputCheckbox extends CFormInput {

    protected $checked = "";
    protected $label = "";
    protected $applyjs = "";
    protected $display_inline = "";
    protected $label_wrap;

    public function __construct($id) {
        parent::__construct($id);


        $this->type = "checkbox";
        $this->label = "";
        $this->applyjs = "uniform";
        $this->checked = false;
        $this->display_inline = false;
        $this->label_wrap = false;
    }

    public static function factory($id) {
        return new CFormInputCheckbox($id);
    }

    public function set_applyjs($applyjs) {
        $this->applyjs = $applyjs;
        return $this;
    }

    public function set_checked($bool) {
        $this->checked = $bool;
        return $this;
    }

    public function set_label($label, $lang = true) {
        if ($lang == true) {
            $label = clang::__($label);
        }
        $this->label = $label;
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
        $html->append('<label class="checkbox" ' . $label_addition_attr . '>');
        if ($this->applyjs == "switch") {
            $html->append('<div class="switch">');
        }
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        if ($this->bootstrap == '3') {
            $classes = $classes ." form-control ";
        }
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $html->append('<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled ' . $classes . '' . $this->validation->validation_class() . '" value="' . $this->value . '"' . $disabled . $custom_css . $checked . '>');
        if (strlen($this->label) > 0) {
            if ($this->label_wrap) {
                $html->appendln('<label for="'.$this->id.'" class="checkbox-label"><span></span>');
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
        }
        if ($this->applyjs == "switch") {
            //$js->append("$('#".$this->id."').parent().bootstrapSwitch();")->br();
        }


        return $js->text();
    }

}
