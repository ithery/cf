<?php

/**
 * Description of Checkbox.
 *
 * @author Alvin
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 14, 2018, 15:35:24 PM
 */
class CElement_FormInput_Checkbox extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Checkbox,
        CTrait_Element_Property_Label;

    protected $checked = '';

    protected $applyjs = '';

    protected $display_inline = '';

    protected $label_wrap;

    protected $style = '';

    public function __construct($id) {
        parent::__construct($id);

        $this->style = 'minimal';
        $this->type = 'checkbox';
        $this->label = '';
        $this->applyjs = 'uniform';
        $this->checked = false;
        $this->display_inline = false;
        $this->label_wrap = false;
        $this->applyjs = c::theme('js_checkbox', 'uniform');
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    public function setChecked($bool) {
        $this->checked = $bool;

        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $disabled = '';
        $checked = '';
        if ($this->checked) {
            $checked = ' checked="checked"';
        }
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }

        $label_addition_attr = '';
        if ($this->display_inline) {
            $label_addition_attr = 'style="display:inline-block;padding-right:5px"';
        }
        $labelClass = 'checkbox';

        $html->append('<label class="' . $labelClass . '" ' . $label_addition_attr . '>');
        if ($this->applyjs == 'switch') {
            $html->append('<div class="switch">');
        }
        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }

        $custom_css = $this->custom_css;
        $custom_css = $this->renderStyle($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = '';
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= ' ' . $k . '="' . $v . '"';
        }
        $html->append('<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled ' . $classes . '' . $this->validation->validationClass() . '" value="' . $this->value . '"' . $disabled . $custom_css . $checked . $addition_attribute . '>');
        //$html->append('<span></span>');
        if (strlen($this->label) > 0) {
            $html->appendln('<label for="' . $this->id . '" class="checkbox-label"><span></span>');
            $html->appendln('&nbsp;' . $this->label);
            $html->appendln('</label>');
        }
        if ($this->applyjs == 'switch') {
            $html->append('</div>');
        }
        $html->append('</label>');
        $html->br();

        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();
        if ($this->applyjs == 'uniform') {
            //$js->append("$('#".$this->id."').uniform();")->br();
        } elseif ($this->applyjs == 'switch') {
            //$js->append("$('#".$this->id."').parent().bootstrapSwitch();")->br();
        } elseif ($this->applyjs == 'icheck') {
            $js->append("
                    $('#" . $this->id . "').iCheck({
                        checkboxClass: 'icheckbox icheckbox_minimal-blue',
                    });
                ");
        }

        return $js->text();
    }
}
