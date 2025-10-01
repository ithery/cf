<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Radio extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Radio,
        CTrait_Element_Property_Label;

    protected $checked;

    // protected $label;
    protected $applyjs;

    protected $label_wrap;

    protected $inline;

    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'radio';
        $this->label = '';
        $this->applyjs = 'uniform';
        $this->checked = false;
        $this->inline = false;
        $this->label_wrap = false;
        $jsRadio = c::theme('radio.js');
        if (strlen($jsRadio) > 0) {
            $this->applyjs = $jsRadio;
        }
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    public function setChecked($bool = true) {
        $this->checked = $bool;

        return $this;
    }

    public function setLabelWrap($bool) {
        $this->label_wrap = $bool;

        return $this;
    }

    public function getInline() {
        return $this->inline;
    }

    public function setInline($inline) {
        $this->inline = $inline;

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
        $label_class = 'radio-inline';

        $addition_attribute = '';
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= ' ' . $k . '="' . $v . '"';
        }

        $html->append('<label class="checkbox' . $classes . '" >');
        if ($this->applyjs == 'switch') {
            $html->append('<div class="switch">');
        }

        $html->append('<input type="radio" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled' . $this->validation->validationClass() . '" ' . $addition_attribute . ' value="' . $this->value . '"' . $disabled . $checked . '>');
        if (strlen($this->label) > 0) {
            $html->appendln('&nbsp;' . $this->label);
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
                        radioClass: 'iradio iradio_minimal-blue'
                    });
                ");
        }

        return $js->text();
    }
}
