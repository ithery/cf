<?php

class CElement_FormInput_Checkbox extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Checkbox,
        CTrait_Element_Property_Label;

    /**
     * @var bool
     */
    protected $checked = '';

    /**
     * Which checkbox-styling plugin to initialize in js(): `uniform`, `switch`
     * or `icheck`.
     *
     * @var string
     */
    protected $applyjs = '';

    /**
     * Whether the label is rendered inline (next to the checkbox) via an
     * inline `display:inline-block` style.
     *
     * @var bool
     */
    protected $display_inline = '';

    /**
     * @var bool
     */
    protected $label_wrap;

    /**
     * @var string
     */
    protected $style = '';

    /**
     * @var string
     */
    protected $themeType = 'checkbox';

    /**
     * @param string $id
     *
     * @return void
     */
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

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setChecked($bool) {
        $this->checked = $bool;

        return $this;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
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
        $html->append('<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" class="input-unstyled ' . $classes . '' . '" value="' . $this->value . '"' . $disabled . $custom_css . $checked . $addition_attribute . '>');
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

    /**
     * @param int $indent
     *
     * @return string
     */
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
